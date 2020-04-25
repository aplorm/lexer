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
use Aplorm\Lexer\Exception\PhpSyntaxException;

/**
 * Analyse token extract from php file with token_get_all method.
 *
 * TODO: Extract Element analyzed in subclass
 */
class TokenAnalyser
{
    /**
     * current analyzed data.
     *
     * @var array<string>
     */
    private static array $buffer = [];

    /**
     * current token.
     *
     * @var array<int|string>
     */
    private static ?array $token = null;

    /**
     * previous analyzed token.
     *
     * @var array<int|string>
     */
    private static ?array $previousToken = null;

    /**
     * previous visibility find during analyzed.
     *
     * @var string|null
     */
    private static ?string $previousVisibility = null;

    /**
     * used during element analyzed to determine if element is nullable.
     *
     * @var bool
     */
    private static bool $nullable = false;

    /**
     * the current element type.
     *
     * @var string|null
     */
    private static ?string $type = null;

    /**
     * the last annotations found during analyzed.
     *
     * @var mixed[]
     *
     * @see DockBlockAnalyser::analyse
     */
    private static ?array $lastAnnotations = null;

    /**
     * all the token find with get_all_token method.
     *
     * @var array<mixed>
     */
    private static array $tokens = [];

    /**
     * current position in token array.
     *
     * @var int
     */
    private static int $iterator = 0;

    /**
     * size of the token array.
     *
     * @var int
     */
    private static int $tokenLength = 0;

    private const NAMESPACE_PART = 'namespace';
    private const CLASS_NAME_PART = 'classname';
    private const CLASS_ALIASES = 'classalias';
    private const VARIABLE_PART = 'variables';
    private const USE_PART = 'use';

    /**
     * parts find during analyzed.
     *
     * @var array<string, mixed>
     */
    private static array $parts = [
        self::NAMESPACE_PART => null,
        self::CLASS_NAME_PART => null,
        self::CLASS_ALIASES => [],
        self::USE_PART => [],
        self::VARIABLE_PART => [],
    ];

    /**
     * analyse tokens from get_all_token.
     *
     * @param array<mixed> $tokens the tokens to analyse
     *
     * @return array<mixed> all the data parsed from tokens
     */
    public static function &analyse(array &$tokens): array
    {
        self::init($tokens);

        while (self::$iterator < self::$tokenLength) {
            self::skip();
            if (self::isA(TokenNameInterface::NAMESPACE_TOKEN)) {
                self::handleNamespace(true);
            } elseif (self::isA(TokenNameInterface::USE_TOKEN)) {
                self::handleNamespace();
            } elseif (self::isA(TokenNameInterface::CLASS_TOKEN)) {
                self::handleClassName();
            } elseif (self::isA(TokenNameInterface::DOC_COMMENT_TOKEN)) {
                self::handleDocComment();
            } elseif (self::isA(TokenNameInterface::VISIBILITY_TOKENS)) {
                self::handleElement();
            }
            // echo self::$token[1], PHP_EOL;
            // self::debug();
            self::next();
        }
        var_dump(self::$parts[self::VARIABLE_PART]);

        return self::$parts;
    }

    /**
     * analyzed class name.
     */
    protected static function handleClassName(): void
    {
        while (self::$iterator < self::$tokenLength && !self::isA(TokenNameInterface::STRING_TOKEN)) {
            self::next();
        }

        self::buffering();
        $className = self::flush();
        /** @var string */
        $namespace = self::getSpecificPart(self::NAMESPACE_PART);
        $fullyClassName = sprintf('%s\\%s', $namespace, $className);

        $classData = [
            'className' => $className,
            'namespace' => $namespace,
            'fullyQualifiedClassName' => $fullyClassName,
            'annotations' => self::$lastAnnotations,
        ];

        self::$lastAnnotations = null;

        self::addDataToPart(self::CLASS_NAME_PART, $className, $classData);
    }

    /**
     * Analyse docBlock.
     *
     * @throws AnnotationSyntaxException if dockblock is not correctly formed
     */
    protected static function handleDocComment(): void
    {
        self::$lastAnnotations = DocBlockAnalyser::analyse(self::tokenValue());
        self::next();
    }

    /**
     * analyse namespace.
     *
     * @param bool|bool $classNamespace set to true if the file namespace
     */
    protected static function handleNamespace(bool $classNamespace = false): void
    {
        self::next();
        $namespaceBase = null;
        $fullNamespace = null;

        while (self::$iterator < self::$tokenLength && !self::isA([
            TokenNameInterface::SEMI_COLON_TOKEN,
        ])) {
            self::skip();

            if (self::isA(TokenNameInterface::OPEN_CURLY_BRACE_TOKEN)) {
                $namespaceBase = self::flush();
            } elseif (self::isA(TokenNameInterface::AS_TOKEN)) {
                if (null !== $namespaceBase) {
                    $fullNamespace = sprintf('%s\\%s', $namespaceBase, self::flush());
                } else {
                    $fullNamespace = self::flush();
                }
            } elseif (self::isA([
                TokenNameInterface::CLOSE_CURLY_BRACE_TOKEN,
                TokenNameInterface::COMMA_TOKEN,
            ])) {
                self::addNamespaceToPart($fullNamespace, $namespaceBase);
                $fullNamespace = $namespaceBase = null;
            } else {
                self::buffering();
            }

            self::next();
        }

        self::addNamespaceToPart($fullNamespace, $namespaceBase);
        if ($classNamespace) {
            self::$parts[self::NAMESPACE_PART] = $fullNamespace;
        }
    }

    /**
     * analyse elemet.
     *
     * @throws PhpSyntaxException on php syntax error
     */
    protected static function handleElement(): void
    {
        self::$previousVisibility = self::tokenValue();
        self::next();
        self::$nullable = false;
        self::$type = null;
        while (self::$iterator < self::$tokenLength && !self::isA([
            TokenNameInterface::VARIABLE_TOKEN,
            TokenNameInterface::FUNCTION_TOKEN,
        ])) {
            self::debug();
            if (self::isA(TokenNameInterface::CONSTANT_TOKEN)) {
                break;
            }

            if (self::isA(TokenNameInterface::QUESTION_MARK_TOKEN)) {
                self::$nullable = true;
                self::next();
                self::skip();
                if (self::isA(TokenNameInterface::STRING_TOKEN)) {
                    self::$type = self::tokenValue();
                } else {
                    throw new PhpSyntaxException('Question mark must followed by a type');
                }
            } elseif (self::isA(TokenNameInterface::STRING_TOKEN)) {
                self::$type = self::tokenValue();
            }
            self::next();
        }
        if (self::isA(TokenNameInterface::VARIABLE_TOKEN)) {
            self::handleVariable();
        }
    }

    /**
     * add object variable to parts with it metadata.
     */
    protected static function handleVariable(): void
    {
        self::buffering();
        $variableName = self::flush();

        $varData = [
            'name' => $variableName,
            'visibility' => self::$previousVisibility,
            'nullable' => self::$nullable,
            'type' => self::$type,
            'annotations' => self::$lastAnnotations,
        ];

        self::$lastAnnotations = null;
        self::$previousVisibility = null;
        self::$nullable = false;
        self::$type = null;
        self::handleVariableDefaultValue();
        self::addDataToPart(self::VARIABLE_PART, $variableName, $varData);
    }

    /**
     * @todo: finish
     */
    protected static function handleVariableDefaultValue(): void
    {
        while (self::$iterator < self::$tokenLength && !self::isA([
            TokenNameInterface::SEMI_COLON_TOKEN,
        ])) {
            self::skip();
            self::debug();
            self::next();
        }
    }

    /**
     * [addNamespaceToPart description].
     *
     * @param string|null $fullNamespace the full namespace
     * @param string|null $baseNamespace a base namespace for group use case
     */
    protected static function addNamespaceToPart(?string &$fullNamespace = null, ?string $baseNamespace = null): void
    {
        if (null === $fullNamespace) {
            $alias = self::previousTokenValue();
            if (null !== $baseNamespace) {
                $fullNamespace = sprintf('%s%s%s', $baseNamespace, TokenNameInterface::NS_SEPARATOR_TOKEN, self::flush());
            } else {
                $fullNamespace = self::flush();
            }
        } else {
            $alias = self::flush();
        }

        self::addDataToPart(self::USE_PART, $fullNamespace);
        self::addDataToPart(self::CLASS_ALIASES, $alias, $fullNamespace);
    }

    /**
     * add tokenvalue to buffer.
     */
    protected static function buffering(): void
    {
        self::$buffer[] = self::tokenValue();
    }

    /**
     * transform buffer into string and reset buffer.
     *
     * @return string the buffer transform into string
     */
    protected static function flush(): string
    {
        $bufferContent = implode('', self::$buffer);
        self::$buffer = [];

        return $bufferContent;
    }

    /**
     * Add data to specific part.
     *
     * @param string $part  the target part
     * @param string $key   the part name as key
     * @param mixed  $value the value of part, true if not provided
     */
    protected static function addDataToPart(string $part, string $key, &$value = true): void
    {
        self::$parts[$part][$key] = $value;
    }

    /**
     * skip all the jumpable token.
     *
     * @return bool if a skip is made return true, false otherwise
     */
    protected static function skip(): bool
    {
        $skip = false;
        while (self::isA(
            [
                TokenNameInterface::WHITESPACE_TOKEN,
                TokenNameInterface::EMPTY_TOKEN,
            ]
        )) {
            self::next();
            $skip = true;
        }

        return $skip;
    }

    /**
     * get the next token.
     */
    protected static function next(): void
    {
        ++self::$iterator;
        self::readToken();
    }

    /**
     * init analyser.
     *
     * @param array<mixed> $tokens the tokens to analyse
     */
    protected static function init(array &$tokens): void
    {
        self::$tokenLength = \count($tokens);
        self::$iterator = 0;
        self::readToken();
        self::$tokens = &$tokens;
    }

    /**
     * read token and normalyse custom token.
     */
    protected static function readToken(): void
    {
        if (\is_string(self::$tokens[self::$iterator])) {
            $value = self::$tokens[self::$iterator];
            self::$tokens[self::$iterator] = [
                self::getCustomToken($value),
                $value,
            ];
        }
        self::$token = &self::$tokens[self::$iterator];

        if (0 !== self::$iterator) {
            self::$previousToken = &self::$tokens[(self::$iterator - 1)];
        }
    }

    /**
     * @return string the token value
     */
    protected static function tokenValue(): string
    {
        return \is_string(self::$token[1]) ? trim(self::$token[1]) : (string) self::$token[1];
    }

    /**
     * @return string the previous value
     */
    protected static function previousTokenValue(): string
    {
        return (string) self::$previousToken[1];
    }

    /**
     * return a specifi part.
     *
     * @param string $part the part name
     *
     * @return array<mixed>|string|bool
     */
    protected static function getSpecificPart(string $part)
    {
        return self::$parts[$part];
    }

    /**
     * get custom token value.
     *
     * @param string $value the unedefined value
     *
     * @return string|null the token find with the value
     */
    protected static function getCustomToken(string &$value): ?string
    {
        return TokenNameInterface::CUSTOM_TOKEN[$value] ?? null;
    }

    /**
     * test if current token is equal or one of provided token.
     *
     * @param int|int[]|string|string[] $tokens the expected type
     */
    protected static function isA($tokens): bool
    {
        if (!\is_array($tokens)) {
            $tokens = [$tokens];
        }

        return \in_array(self::$token[0], $tokens, true);
    }

    protected static function debug(): void
    {
        if (\is_int(self::$token[0])) {
            echo self::$token[0], ' ', token_name(self::$token[0]), ' ', self::$token[1], '' , PHP_EOL;
        } else {
            echo self::$token[0], ' ', 'CUSTOM_TOKEN', ' ', self::$token[1], '' , PHP_EOL;
        }
    }
}
