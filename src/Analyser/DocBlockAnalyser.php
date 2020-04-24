<?php

declare(strict_types=1);

namespace Orm\Lexer\Analyser;

use Orm\Lexer\Exception\AnnotationSyntaxException;

class DocBlockAnalyser
{
    protected static array $tokens = [];

    protected static array $buffer = [];

    protected static int $iterator = 0;

    protected static int $tokenLength = 0;

    protected static ?string $token = '';

    protected const PARAM_VALUE_KEY = 'value';

    protected const PARAM_NAME_KEY = 'name';

    protected const PARAM_TYPE_KEY = 'type';

    protected const STRING_TYPE = 1;
    protected const CLASS_CONSTANT_TYPE = 2;
    protected const OTHER_CONSTANT_TYPE = 3;
    protected const NUMBER_CONSTANT_TYPE = 4;
    protected const ARRAY_TYPE = 5;
    protected const OBJECT_TYPE = 6;
    protected const ANNOTATION_TYPE = 7;

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

    public static function &analyse(string $blocComment): array
    {
        $annotations = [];
        self::init($blocComment);
        while (self::$iterator < self::$tokenLength) {
            self::skip();
            if (self::isA(DocBlockTokenInterface::AROBASE_TOKEN)) {
                $annotation = self::handleAnnotations();
                $annotations[] = $annotation;
            }

            self::next();
        }

        return $annotations;
    }

    private static function handleAnnotations(): array
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

        $annotation['name'] = self::flush();

        if (self::isA(DocBlockTokenInterface::OPEN_PARENTHESIS_TOKEN)) {
            $annotation['params'] = self::handleAnnotationParams($annotation['name']);
        }

        return $annotation;
    }

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
            $lastParam = self::handleParams($annotation);
            if (self::isA(DocBlockTokenInterface::EQUAL_TOKEN)) {
                if (self::OTHER_CONSTANT_TYPE !== $lastParam[self::PARAM_TYPE_KEY]) {
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

    protected static function handleParams(string $annotation)
    {
        self::skip();
        $param = null;
        if (self::isA(self::OPEN_STRING_TOKEN)) {
            $param = [
                self::PARAM_TYPE_KEY => self::STRING_TYPE,
                self::PARAM_VALUE_KEY => self::handleStringParam($annotation),
            ];
        } elseif (self::isA(DocBlockTokenInterface::OPEN_CURLY_BRACE_TOKEN)) {
            $param = [
                self::PARAM_TYPE_KEY => self::OBJECT_TYPE,
                self::PARAM_VALUE_KEY => self::handleObject($annotation),
            ];
        } elseif (self::isA(DocBlockTokenInterface::AROBASE_TOKEN)) {
            $param = [
                self::PARAM_TYPE_KEY => self::ANNOTATION_TYPE,
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

    protected static function handleConstant(string $annotation)
    {
        while (self::$iterator < self::$tokenLength && !self::isA([
            PHP_EOL,
            DocBlockTokenInterface::EMPTY_TOKEN,
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

    protected static function getConstantType($constant)
    {
        if (empty($constant)) {
            return self::OTHER_CONSTANT_TYPE;
        }

        if (false !== strpos($constant, '::')) {
            return self::CLASS_CONSTANT_TYPE;
        }

        if (is_numeric($constant)) {
            return self::NUMBER_CONSTANT_TYPE;
        }

        return self::OTHER_CONSTANT_TYPE;
    }

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

    protected static function handleObject(string $annotation): array
    {
        $params = [];
        $currentKey = null;
        self::next();
        while (self::$iterator < self::$tokenLength && !self::isA(DocBlockTokenInterface::CLOSE_CURLY_BRACE_TOKEN)) {
            self::skip();
            $param = self::handleParams($annotation);

            if (self::isA(self::OBJECT_SEPARATOR_TOKEN)) {
                $currentKey = $param;
            } elseif (self::isA([
                DocBlockTokenInterface::COMMA_TOKEN,
                DocBlockTokenInterface::EMPTY_TOKEN,
                DocBlockTokenInterface::CLOSE_CURLY_BRACE_TOKEN,
            ])) {
                if (null === $currentKey && (null === $param || empty($param['value']))) {
                    throw new AnnotationSyntaxException('object not correcly formed for annotation : '.$annotation);
                }
                if (null === $currentKey) {
                    $params[] = $param;
                } else {
                    $params[$currentKey[self::PARAM_VALUE_KEY]] = $param;
                    $currentKey = null;
                }
            } else {
                throw new AnnotationSyntaxException('object not correcly formed for annotation : '.$annotation.' on key or value : '.($currentKey ? $currentKey : $param));
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

    protected static function tokenise(string &$blocComment)
    {
        self::$tokens = str_split($blocComment);
    }

    protected static function init(string &$blocComment)
    {
        self::tokenise($blocComment);

        self::$tokenLength = \count(self::$tokens);
        self::$iterator = 0;
        self::$token = self::tokenValue();
    }

    protected static function next()
    {
        ++self::$iterator;
        self::$token = self::tokenValue();

        if (null !== self::$token) {
            self::$token = trim(self::$token);
        }
    }

    protected static function buffering()
    {
        self::$buffer[] = self::tokenValue();
    }

    protected static function flush(): ?string
    {
        $bufferContent = implode('', self::$buffer);
        self::$buffer = [];

        return $bufferContent;
    }

    protected static function skip()
    {
        while (self::isA(self::SKIPPED_TOKEN)) {
            self::next();
        }

        return false;
    }

    protected static function isA($tokens): bool
    {
        if (!\is_array($tokens)) {
            $tokens = [$tokens];
        }

        return \in_array(self::$token, $tokens, true);
    }

    protected static function tokenValue()
    {
        return self::$tokens[self::$iterator] ?? null;
    }
}
