<?php
/**
 *  This file is part of the Aplorm package.
 *
 *  (c) Nicolas Moral <n.moral@live.fr>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Aplorm\Lexer\Analyser;

use Aplorm\Lexer\Exception\AnnotationSyntaxException;
use Aplorm\Common\Interpreter\TypeInterface;

/**
 * Analyse a docBlock to extract Annotation data.
 */
class DocBlockAnalyser
{
    /**
     * all the tokens find in docbloc.
     *
     * @var array<string>
     */
    protected static array $tokens = [];

    /**
     * contains the current analyzed value.
     *
     * @var array<string>
     */
    protected static array $buffer = [];

    /**
     * position in self::$tokens.
     */
    protected static int $iterator = 0;

    /**
     * number of tokens.
     */
    protected static int $tokenLength = 0;

    /**
     * current token.
     *
     * @var string
     */
    protected static ?string $token = '';

    protected const PARAM_VALUE_KEY = 'value';

    protected const PARAM_NAME_KEY = 'name';

    protected const PARAM_TYPE_KEY = 'type';

    /**
     * token that open string in annotation.
     */
    protected const OPEN_STRING_TOKEN = [
        '"',
        '\'',
    ];

    protected const OBJECT_SEPARATOR_TOKEN = [
        DocBlockTokenInterface::COLON_TOKEN,
        DocBlockTokenInterface::EQUAL_TOKEN,
    ];

    protected const SKIPPED_TOKEN = [
        PHP_EOL,
        DocBlockTokenInterface::STAR_TOKEN,
        DocBlockTokenInterface::SLASH_TOKEN,
        DocBlockTokenInterface::EMPTY_TOKEN,
    ];

    protected const EXCLUED_ANNOTATIONS = [
        'example',
        'internal',
        'inheritdoc',
        'link',
        'see',
        'api',
        'author',
        'category',
        'copyright',
        'deprecated',
        'example',
        'filesource',
        'global',
        'ignore',
        'internal',
        'license',
        'link',
        'method',
        'package',
        'property',
        'property-read',
        'property-write',
        'see',
        'since',
        'source',
        'subpackage',
        'throws',
        'todo',
        'uses',
        'used-by',
        'version',
    ];

    protected const TYPE_ANNOTATIONS = [
        'var',
        'param',
        'return',
    ];

    /**
     * @param string $blocComment The docBloc who may contains annotation
     *
     * @throws AnnotationSyntaxException if dockblock is not correctly formed
     *
     * @return mixed[] list of all anotation found in docBloc
     */
    public static function &analyse(string $blocComment): array
    {
        $annotations = [];
        self::init($blocComment);
        while (self::$iterator < self::$tokenLength) {
            self::skip();
            if (self::isA(DocBlockTokenInterface::AROBASE_TOKEN)) {
                $annotation = self::handleAnnotations();
                if (null !== $annotation) {
                    $annotations[] = $annotation;
                }
            }

            self::next();
        }

        return $annotations;
    }

    /**
     * Analyse an annoation in docBloc.
     *
     * @throws AnnotationSyntaxException if annotation could not be parsed
     *
     * @return array<string, string|array> the current annotations
     */
    private static function handleAnnotations(): ?array
    {
        $annotation = [
            'name' => null,
            'params' => [],
        ];
        self::next();

        while (self::$iterator < self::$tokenLength && !self::isA([
            PHP_EOL,
            DocBlockTokenInterface::OPEN_PARENTHESIS_TOKEN,
            DocBlockTokenInterface::EMPTY_TOKEN,
            DocBlockTokenInterface::SEMICOLON_TOKEN,
            DocBlockTokenInterface::COMMA_TOKEN,
        ])) {
            self::buffering();
            self::next();
        }

        if (self::$iterator >= self::$tokenLength) {
            throw new AnnotationSyntaxException('Unable to parse docblock got : '.self::flush());
        }
        $name = self::flush();

        if (\in_array($name, self::EXCLUED_ANNOTATIONS, true)) {
            return null;
        }
        $annotation['name'] = $name;
        if (\in_array($name, self::TYPE_ANNOTATIONS, true)) {
            $annotation['types'] = self::handleTypeAnnotations();
        } elseif (self::isA(DocBlockTokenInterface::OPEN_PARENTHESIS_TOKEN)) {
            $annotation['params'] = self::handleAnnotationParams($annotation['name']);
        }

        return $annotation;
    }

    /**
     * handle params of an annotation.
     *
     * @param string $annotation the current annotations name
     *
     * @throws AnnotationSyntaxException if params is not correctly setup in annotation
     *
     * @return array<array<string, string|array>> an array of params analyzed
     */
    protected static function handleAnnotationParams(string $annotation): array
    {
        $params = [];
        self::next();
        $paramName = null;
        $lastParam = null;
        while (self::$iterator < self::$tokenLength && !self::isA(DocBlockTokenInterface::CLOSE_PARENTHESIS_TOKEN)) {
            self::skip();
            if (self::isA(DocBlockTokenInterface::EQUAL_TOKEN)) {
                if (null === $lastParam) {
                    throw new AnnotationSyntaxException('missing name for param');
                }
            }
            $lastParam = self::handleParam($annotation);
            if (self::isA(DocBlockTokenInterface::EQUAL_TOKEN)) {
                if (TypeInterface::OTHER_CONSTANT_TYPE !== $lastParam[self::PARAM_TYPE_KEY]) {
                    throw new AnnotationSyntaxException('param name must be a constant');
                }

                $paramName = $lastParam[self::PARAM_VALUE_KEY];
                $lastParam = null;
            } elseif (null !== $lastParam) {
                if (null !== $paramName) {
                    $lastParam[self::PARAM_NAME_KEY] = $paramName;
                }

                $params[] = $lastParam;

                $paramName = $lastParam = null;
            }

            if (self::isA(DocBlockTokenInterface::CLOSE_PARENTHESIS_TOKEN)) {
                break;
            }

            self::next();
        }

        self::next();

        return $params;
    }

    protected static function handleTypeAnnotations(): array
    {
        $types = [
            'paramTypes' => [],
        ];
        self::skip();

        while (self::$iterator < self::$tokenLength && !self::isA(self::SKIPPED_TOKEN)) {
            if (self::isA(DocBlockTokenInterface::PIPE_TOKEN)) {
                $types['paramTypes'][] = self::flush();
                self::next();
            }
            self::buffering();
            self::next();
        }
        $types['paramTypes'][] = self::flush();

        self::skip();
        if (self::isA(DocBlockTokenInterface::DOLLAR_TOKEN)) {
            while (self::$iterator < self::$tokenLength && !self::isA(self::SKIPPED_TOKEN)) {
                self::buffering();
                self::next();
            }

            $types['param'] = self::flush();
        }

        return $types;
    }

    /**
     * handle param of an annotation.
     *
     * @param string $annotation the current annotation
     *
     * @return array<string, mixed>|null         the param analyzed
     */
    protected static function handleParam(string $annotation)
    {
        self::skip();
        $param = null;
        if (self::isA(self::OPEN_STRING_TOKEN)) {
            $param = [
                self::PARAM_TYPE_KEY => TypeInterface::STRING_TYPE,
                self::PARAM_VALUE_KEY => self::handleStringParam($annotation),
            ];
        } elseif (self::isA(DocBlockTokenInterface::OPEN_CURLY_BRACE_TOKEN)) {
            $param = [
                self::PARAM_TYPE_KEY => TypeInterface::OBJECT_TYPE,
                self::PARAM_VALUE_KEY => self::handleObject($annotation),
            ];
        } elseif (self::isA(DocBlockTokenInterface::AROBASE_TOKEN)) {
            $param = [
                self::PARAM_TYPE_KEY => TypeInterface::ANNOTATION_TYPE,
                self::PARAM_VALUE_KEY => self::handleAnnotations(),
            ];
        } else {
            $paramValue = self::handleConstant($annotation);
            $param = [
                self::PARAM_TYPE_KEY => self::getConstantType($paramValue),
                self::PARAM_VALUE_KEY => $paramValue,
            ];
        }
        self::skip();

        return $param;
    }

    /**
     * handle constant param such has boolean value, number value.
     *
     * @param string $annotation the current annotation
     *
     * @throws AnnotationSyntaxException if constant is not correctly formed
     *
     * @return string|null the constant value
     */
    protected static function handleConstant(string $annotation): ?string
    {
        while (self::$iterator < self::$tokenLength && !self::isA([
            PHP_EOL,
            DocBlockTokenInterface::EMPTY_TOKEN,
            DocBlockTokenInterface::EQUAL_TOKEN,
            DocBlockTokenInterface::COMMA_TOKEN,
            DocBlockTokenInterface::CLOSE_CURLY_BRACE_TOKEN,
            DocBlockTokenInterface::CLOSE_PARENTHESIS_TOKEN,
        ])) {
            self::buffering();
            self::next();
        }

        if (self::$iterator >= self::$tokenLength) {
            throw new AnnotationSyntaxException('Constant not correcly formed for annotation : '.$annotation.', got '.self::flush());
        }

        return self::flush();
    }

    /**
     * anaylzed constant and retur the correspondant type.
     *
     * @param mixed $constant the analyzed constant
     *
     * @return int the analzyd constant type
     *
     * @see the type constant in DockBlockAnalyser class
     */
    protected static function getConstantType($constant): int
    {
        if (empty($constant)) {
            return TypeInterface::OTHER_CONSTANT_TYPE;
        }

        if (false !== strpos($constant, '::')) {
            return TypeInterface::CLASS_CONSTANT_TYPE;
        }

        if (is_numeric($constant)) {
            return TypeInterface::NUMBER_CONSTANT_TYPE;
        }

        return TypeInterface::OTHER_CONSTANT_TYPE;
    }

    /**
     * handle string params.
     *
     * @param string $annotation the current annotation name
     *
     * @throws AnnotationSyntaxException if object is not correctly formed
     *
     * @return string the string value
     */
    protected static function handleStringParam(string $annotation): string
    {
        $startToken = self::tokenValue();
        $isEscaped = false;

        self::next();
        while (self::$iterator < self::$tokenLength && (!self::isA($startToken) || ($isEscaped && self::isA($startToken)))) {
            if (self::isA(DocBlockTokenInterface::ANTI_SLASH_TOKEN) && !$isEscaped) {
                $isEscaped = true;
                self::next();

                continue;
            }
            self::buffering();
            $isEscaped = false;
            self::next();
        }
        self::next();
        if (self::isA(self::OPEN_STRING_TOKEN)) {
            throw new AnnotationSyntaxException('string not correcly formed for annotation : '.$annotation.', got '.self::tokenValue());
        }
        if (self::$iterator >= self::$tokenLength) {
            throw new AnnotationSyntaxException('string not correcly formed for annotation : '.$annotation.', got '.self::flush());
        }

        return self::flush();
    }

    /**
     * handle object has parameter.
     *
     * @param string $annotation the current annotation
     *
     * @throws AnnotationSyntaxException if object is not correctly formed
     *
     * @return array<mixed, mixed> if object is not correctly formed
     */
    protected static function handleObject(string $annotation): array
    {
        $params = [];
        $currentKey = null;
        self::next();
        while (self::$iterator < self::$tokenLength && !self::isA(DocBlockTokenInterface::CLOSE_CURLY_BRACE_TOKEN)) {
            self::skip();
            $param = self::handleParam($annotation);

            if (self::isA(self::OBJECT_SEPARATOR_TOKEN)) {
                $currentKey = $param;
            } elseif (self::isA([
                DocBlockTokenInterface::COMMA_TOKEN,
                DocBlockTokenInterface::EMPTY_TOKEN,
                DocBlockTokenInterface::CLOSE_CURLY_BRACE_TOKEN,
            ]) && null !== $param) {
                if (null === $currentKey && empty($param[self::PARAM_VALUE_KEY])) {
                    throw new AnnotationSyntaxException('object not correcly formed for annotation : '.$annotation);
                }
                if (null === $currentKey) {
                    $params[] = $param;
                } else {
                    $params[$currentKey[self::PARAM_VALUE_KEY]] = $param;
                    $currentKey = null;
                }
            } else {
                throw new AnnotationSyntaxException('object not correcly formed for annotation : '.$annotation);
            }

            if (self::isA([
                DocBlockTokenInterface::EMPTY_TOKEN,
                DocBlockTokenInterface::CLOSE_CURLY_BRACE_TOKEN,
            ])) {
                break;
            }
            self::next();
        }

        self::next();

        return $params;
    }

    /**
     * split docBlock into analyzable.
     *
     * @param string $blocComment the docBlock to analyse
     */
    protected static function tokenise(string &$blocComment): void
    {
        self::$tokens = str_split($blocComment);
    }

    /**
     * init Analyser.
     *
     * @param string $blocComment the docBlock to analyse
     */
    protected static function init(string &$blocComment): void
    {
        self::tokenise($blocComment);

        self::$tokenLength = \count(self::$tokens);
        self::$iterator = 0;
        self::$token = self::tokenValue();
    }

    /**
     * move interator to the next cursor.
     */
    protected static function next(): void
    {
        ++self::$iterator;
        self::$token = self::tokenValue();

        if (null !== self::$token) {
            self::$token = trim(self::$token);
        }
    }

    /**
     * add current token into buffer.
     */
    protected static function buffering(): void
    {
        self::$buffer[] = self::tokenValue();
    }

    /**
     * transform buffer into string.
     *
     * @return ?string the buffer transform into string
     */
    protected static function flush(): ?string
    {
        $bufferContent = implode('', self::$buffer);
        self::$buffer = [];

        return $bufferContent;
    }

    /**
     * move iterator while token are in skipped_token.
     *
     * @return false to allow used into loop
     */
    protected static function skip(): bool
    {
        while (self::$iterator < self::$tokenLength && self::isA(self::SKIPPED_TOKEN)) {
            self::next();
        }

        return false;
    }

    /**
     * test if current token is equal or one of provided token.
     *
     * @param string|string[] $tokens the expected type
     */
    protected static function isA($tokens): bool
    {
        if (!\is_array($tokens)) {
            $tokens = [$tokens];
        }

        return \in_array(self::$token, $tokens, true);
    }

    /**
     * return the current token value.
     *
     * @return string|null the current token value
     */
    protected static function tokenValue(): ?string
    {
        return self::$tokens[self::$iterator] ?? null;
    }
}
