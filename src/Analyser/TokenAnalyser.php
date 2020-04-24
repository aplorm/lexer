<?php

declare(strict_types=1);

namespace Orm\Lexer\Analyser;

use Orm\Lexer\Exception\PhpSyntaxException;

class TokenAnalyser
{
    private static array $buffer = [];

    private static ?array $token = null;

    private static ?array $previousToken = null;

    private static ?string $previousVisibility = null;

    private static bool $nullable = false;

    private static ?string $type = null;

    private static ?array $lastAnnotations = null;

    private static array $tokens = [];

    private static int $iterator = 0;

    private static int $tokenLength = 0;

    private const NAMESPACE_PART = 'namespace';
    private const CLASS_NAME_PART = 'classname';
    private const CLASS_ALIASES = 'classalias';
    private const VARIABLE_PART = 'variables';
    private const USE_PART = 'use';

    private static array $parts = [
        self::NAMESPACE_PART => null,
        self::CLASS_NAME_PART => null,
        self::CLASS_ALIASES => [],
        self::USE_PART => [],
        self::VARIABLE_PART => [],
    ];

    public static function analyse(array &$tokens): void
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
            } else if (self::isA(TokenNameInterface::VISIBILITY_TOKENS)) {
                self::handleElement();
            }
            // echo self::$token[1], PHP_EOL;
            // self::debug();
            self::next();
        }
        var_dump(self::$parts[self::VARIABLE_PART]);
    }

    protected static function handleClassName()
    {
        while (self::$iterator < self::$tokenLength && !self::isA(TokenNameInterface::STRING_TOKEN)) {
            self::next();
        }

        self::buffering();
        $className = self::flush();
        $namespace = self::getSpecificPart(self::NAMESPACE_PART);
        $fullyClassName = sprintf('%s\\%s', $namespace, $className);

        $classData = [
            'className' => $className,
            'namespace' => $namespace,
            'fullyQualifiedClassName' => $fullyClassName,
            'annotations' => self::$lastAnnotations,
        ];

        self::$lastAnnotations = null;

        self::addBufferToPart(self::CLASS_NAME_PART, $className, $classData);
    }

    protected static function handleDocComment()
    {
        self::$lastAnnotations = DocBlockAnalyser::analyse(self::tokenValue());
        self::next();
    }

    protected static function handleNamespace(bool $classNamespace = false)
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
                self::addNamespaceToPart($fullNamespace, $baseNamespace);
                $fullNamespace = $namespaceBase = null;
            } else {
                self::buffering();
            }

            self::next();
        }

        self::addNamespaceToPart($fullNamespace, $baseNamespace);
        if ($classNamespace) {
            self::$parts[self::NAMESPACE_PART] = $fullNamespace;
        }
    }

    protected static function handleElement()
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
            // self::skip();
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

    protected static function handleVariable()
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
        self::addBufferToPart(self::VARIABLE_PART, $variableName, $varData);
    }

    protected static function handleVariableDefaultValue()
    {
        while (self::$iterator < self::$tokenLength && !self::isA([
            TokenNameInterface::SEMI_COLON_TOKEN,
        ])) {
            self::skip();
            self::debug();
            self::next();
        }
    }

    protected static function addNamespaceToPart(?string &$fullNamespace = null, ?string $baseNamespace = null)
    {
        if (null === $fullNamespace) {
            $alias = self::previousTokenValue();
            if (null !== $namespaceBase) {
                $fullNamespace = sprintf('%s%s%s', $namespaceBase, TokenNameInterface::NS_SEPARATOR_TOKEN, self::flush());
            } else {
                $fullNamespace = self::flush();
            }
        } else {
            $alias = self::flush();
        }

        self::addBufferToPart(self::USE_PART, $fullNamespace);
        self::addBufferToPart(self::CLASS_ALIASES, $alias, $fullNamespace);
    }

    protected static function buffering()
    {
        self::$buffer[] = self::tokenValue();
    }

    protected static function flush()
    {
        $bufferContent = implode('', self::$buffer);
        self::$buffer = [];

        return $bufferContent;
    }

    protected static function addBufferToPart(string $part, string $key, &$value = true)
    {
        self::$parts[$part][$key] = $value;
    }

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

    protected static function next()
    {
        ++self::$iterator;
        self::readToken();
    }

    protected static function init(array &$tokens): void
    {
        self::$tokenLength = \count($tokens);
        self::$iterator = 0;
        self::readToken();
        self::$tokens = &$tokens;
    }

    protected static function readToken()
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

    protected static function tokenValue(): string
    {
        return trim(self::$token[1]);
    }

    protected static function previousTokenValue()
    {
        return self::$previousToken[1];
    }

    protected static function getSpecificPart(string $part)
    {
        return self::$parts[$part];
    }

    protected static function getCustomToken(string &$value)
    {
        return TokenNameInterface::CUSTOM_TOKEN[$value] ?? null;
    }

    protected static function isA($tokens): bool
    {
        if (!\is_array($tokens)) {
            $tokens = [$tokens];
        }

        return \in_array(self::$token[0], $tokens, true);
    }

    protected static function debug()
    {   if (\is_int(self::$token[0])) {
            echo self::$token[0], ' ', token_name(self::$token[0]), ' ', self::$token[1], '' , PHP_EOL;
        } else {
            echo self::$token[0], ' ', 'CUSTOM_TOKEN', ' ', self::$token[1], '' , PHP_EOL;
        }
    }
}
