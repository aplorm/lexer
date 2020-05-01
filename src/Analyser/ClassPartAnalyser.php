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

use Aplorm\Common\Lexer\LexedPartInterface;
use Aplorm\Lexer\Analyser\Traits\CommonAnalyserTraits;

/**
 * Analyse variable and function part of a class.
 */
class ClassPartAnalyser
{
    use CommonAnalyserTraits;

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
     */
    private static ?string $previousVisibility = null;

    /**
     * used during element analyzed to determine if element is nullable.
     */
    private static bool $nullable = false;

    /**
     * used during element analyzed to determine if element is static.
     */
    private static bool $isStatic = false;

    /**
     * the current element type.
     */
    private static ?string $type = null;

    /**
     * Set to true is default value of an elemeent is a constant.
     */
    private static ?bool $isValueAConstant = null;

    /**
     * the last annotations found during analyzed.
     *
     * @var mixed[]
     *
     * @see DockBlockAnalyser::analyse
     */
    private static ?array $lastAnnotations = null;

    /**
     * tokens aray find with token_get_all.
     *
     * @var array<mixed>
     */
    private static array    $tokens = [];
    private static int      $iterator = 0;
    private static int      $tokenLength = 0;

    /**
     * init variable analyser with data from Token Analyser.
     *
     * @param array<mixed> $tokens      list of token find with token_get_all
     * @param int          $iterator    index of current analyse
     * @param int          $tokenLength size of tokens
     */
    public static function init(array &$tokens, int &$iterator, int $tokenLength): void
    {
        self::$tokens = &$tokens;
        self::$iterator = &$iterator;
        self::$tokenLength = $tokenLength;
    }

    /**
     * Analyse function and class attributes.
     *
     * @param array<mixed> $lastAnnotations last annotation find
     *
     * @return array<mixed> function or attribute data
     */
    public static function analyse(?array &$lastAnnotations = null): array
    {
        self::$lastAnnotations = &$lastAnnotations;

        return self::handleElement();
    }

    /**
     * clean analyser of useless data.
     */
    public static function clean(): void
    {
        self::$buffer = [];
        self::$token = null;
        self::$previousToken = null;
        self::$previousVisibility = null;
        self::$nullable = false;
        self::$type = null;
        self::$lastAnnotations = null;
        self::$tokens = [];
        self::$iterator = 0;
        self::$tokenLength = 0;
    }

    /**
     * analyse elemet.
     *
     * @return array<string, string|array<mixed>|null>
     */
    protected static function handleElement(): array
    {
        self::reset();
        self::$nullable = false;
        self::$isStatic = false;
        self::$type = null;
        self::$previousVisibility = 'public';
        while (self::$iterator < self::$tokenLength && !self::isA([
            TokenNameInterface::VARIABLE_TOKEN,
            TokenNameInterface::FUNCTION_TOKEN,
        ])) {
            if (self::isA(TokenNameInterface::VISIBILITY_TOKENS)) {
                self::$previousVisibility = self::tokenValue();
            }

            if (self::isA(TokenNameInterface::STATIC_TOKEN)) {
                self::$isStatic = true;
            }
            if (self::isA(TokenNameInterface::CONSTANT_TOKEN)) {
                break;
            }

            $data = self::handleElementData();
            self::$nullable = $data['nullable'];
            self::$type = $data['type'];
        }

        if (self::isA(TokenNameInterface::VARIABLE_TOKEN)) {
            return self::handleVariable();
        }
        if (self::isA(TokenNameInterface::FUNCTION_TOKEN)) {
            return self::handleFunction();
        }

        return ['partType' => null, 'partName' => null, 'partData' => null];
    }

    /**
     * handle type and nullable part for class attribute or function parameter.
     *
     * @return array<mixed> return nullable and type value for element;
     */
    protected static function handleElementData(): array
    {
        $data = [
            'nullable' => false,
            'type' => null,
        ];

        if (self::isA(TokenNameInterface::QUESTION_MARK_TOKEN)) {
            $data['nullable'] = true;
            self::next();
            self::skip();
            if (self::isA(TokenNameInterface::STRING_TOKEN)) {
                $data['type'] = self::tokenValue();
                self::next();
            }
        } elseif (self::isA(TokenNameInterface::STRING_TOKEN)) {
            $data['type'] = self::tokenValue();
            self::next();
        }
        self::next();

        return $data;
    }

    /**
     * add object variable to parts with it metadata.
     *
     * @return array<string, mixed>
     */
    protected static function handleVariable(): array
    {

        if (self::isA(TokenNameInterface::AND_TOKEN)) {
            self::next();
        }

        self::buffering();
        $variableName = str_replace(['$', '&'], '', self::flush());

        $varData = [
            'name' => $variableName,
            'visibility' => self::$previousVisibility,
            'nullable' => self::$nullable,
            'type' => self::$type,
            'static' => self::$isStatic,
            'annotations' => empty(self::$lastAnnotations) ? null : self::$lastAnnotations,
            'isValueAConstant' => false,
        ];

        self::$lastAnnotations = null;
        self::$previousVisibility = null;
        self::$nullable = false;
        self::$type = null;
        $varData['value'] = self::handleVariableDefaultValue($variableName);
        if (null !== self::$isValueAConstant) {
            $varData['isValueAConstant'] = self::$isValueAConstant;
        }

        return ['partType' => LexedPartInterface::VARIABLE_PART, 'partName' => &$variableName, 'partData' => &$varData];
    }

    /**
     * add object variable to parts with it metadata.
     *
     * @return array<string, string|array<mixed>>
     */
    protected static function handleFunction(): array
    {
        self::next();
        self::skip();
        self::buffering();
        $functionName = self::flush();

        $funcData = [
            'name' => $functionName,
            'visibility' => self::$previousVisibility,
            'nullable' => self::$nullable,
            'static' => self::$isStatic,
            'annotations' => empty(self::$lastAnnotations) ? null : self::$lastAnnotations,
            'returnType' => [],
        ];

        self::$lastAnnotations = null;
        self::$previousVisibility = null;
        self::$nullable = false;
        self::$type = null;

        $funcData['parameters'] = self::handleParameters();

        self::next();
        self::skip();

        if (self::isA(TokenNameInterface::COLON_TOKEN)) {
            self::next();
            self::skip();
            $funcData['returnType'] = self::handleReturnData();
        }

        if (self::isA(TokenNameInterface::OPEN_CURLY_BRACE_TOKEN)) {
            self::handleFunctionContent();
        } elseif (self::isA(TokenNameInterface::SEMI_COLON_TOKEN)) {
            self::next();
            self::skip();
        }
        self::next();

        return ['partType' => LexedPartInterface::METHOD_PART, 'partName' => &$functionName, 'partData' => &$funcData];
    }

    /**
     * find and extract default value for class attribute.
     *
     * @param string $variableName the current variable name
     *
     * @return string|array<mixed>|null
     */
    protected static function handleVariableDefaultValue(string $variableName)
    {
        self::next();
        self::skip();
        self::$isValueAConstant = null;
        if (!self::isA(TokenNameInterface::EQUAL_TOKEN)) {
            return null;
        }

        self::next();
        while (self::$iterator < self::$tokenLength && !self::isA([
            TokenNameInterface::SEMI_COLON_TOKEN,
            TokenNameInterface::COMMA_TOKEN,
            TokenNameInterface::CLOSE_PARENTHESIS_TOKEN,
        ])) {
            self::skip();

            if (self::isA([
                TokenNameInterface::SEMI_COLON_TOKEN,
                TokenNameInterface::COMMA_TOKEN,
                TokenNameInterface::CLOSE_PARENTHESIS_TOKEN,
            ])) {
                break;
            }

            if (self::isA(TokenNameInterface::STRING_TOKEN)) {
                self::$isValueAConstant = true;
                while (self::$iterator < self::$tokenLength
                    && !(
                        self::isA(TokenNameInterface::SKIPPED_TOKENS)
                        || self::isA([
                            TokenNameInterface::SEMI_COLON_TOKEN,
                            TokenNameInterface::COMMA_TOKEN,
                            TokenNameInterface::CLOSE_PARENTHESIS_TOKEN,
                        ])
                    )
                ) {
                    self::buffering();
                    self::next();
                    self::skip();
                }

                break;
            }
            if (self::isA(TokenNameInterface::VALUE_TOKENS)) {
                self::$isValueAConstant = false;
                self::buffering();
            } elseif (self::isA(TokenNameInterface::OPEN_ARRAY_TOKENS)) {
                self::$isValueAConstant = false;

                return self::handleStaticArray();
            }

            self::next();
        }

        return self::cleanStringValue(self::flush());
    }

    /**
     * Handle function parameters.
     *
     * @return array<mixed>
     */
    protected static function handleParameters(): array
    {
        $parameters = [];

        do {
            self::next();
        } while (self::$iterator < self::$tokenLength && !self::isA(TokenNameInterface::OPEN_PARENTHESIS_TOKEN));
        self::next();
        self::skip();
        while (self::$iterator < self::$tokenLength && !self::isA(TokenNameInterface::CLOSE_PARENTHESIS_TOKEN)) {
            if (!self::isA(TokenNameInterface::VARIABLE_TOKEN)) {
                $data = self::handleElementData();
                self::$nullable = $data['nullable'];
                self::$type = $data['type'];
                self::skip();
            }

            [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
            ] = self::handleVariable();

            unset($partData['annotations'], $partData['visibility']);
            $parameters[$partName] = $partData;
            if (self::isA(TokenNameInterface::CLOSE_PARENTHESIS_TOKEN)) {
                break;
            }
            self::next();
            self::skip();
        }

        return $parameters;
    }

    /**
     * Handle array parameters.
     *
     * @return array<mixed> the array find during analyse
     */
    protected static function handleStaticArray(): array
    {
        $arrayValue = [];
        $value = null;
        $key = null;
        if (self::isA(TokenNameInterface::ARRAY_TOKEN)) {
            self::next();
        }
        self::next();
        while (self::$iterator < self::$tokenLength || self::isA(TokenNameInterface::CLOSE_ARRAY_TOKENS)) {
            self::skip();
            if (self::isA(TokenNameInterface::DOUBLE_ARROW_TOKEN) && null !== $value) {
                /** @var string */
                $key = $value;
                $value = null;
                self::next();
                self::skip();
            }

            if (null !== $value) {
                if (null !== $key) {
                    $arrayValue[$key] = $value;
                } else {
                    $arrayValue[] = $value;
                }

                $key = $value = null;
            }

            if (self::isA(TokenNameInterface::CLOSE_ARRAY_TOKENS)) {
                break;
            }

            if (self::isA(TokenNameInterface::OPEN_ARRAY_TOKENS)) {
                $value = self::handleStaticArray();
            } elseif (!self::isA(TokenNameInterface::COMMA_TOKEN)) {
                $value = self::cleanStringValue(self::tokenValue());
            }

            self::next();
        }

        return $arrayValue;
    }

    /**
     * @return array<mixed> the return default value data
     */
    protected static function handleReturnData(): array
    {
        return self::handleElementData();
    }

    protected static function handleFunctionContent(): void
    {
        $nbOpenCurlyBrace = 1;

        while ($nbOpenCurlyBrace > 0 && self::$iterator < self::$tokenLength) {
            self::next();
            self::skip();
            if (self::isA(TokenNameInterface::OPEN_CURLY_BRACE_TOKEN)) {
                ++$nbOpenCurlyBrace;

                continue;
            }

            if (self::isA(TokenNameInterface::CLOSE_CURLY_BRACE_TOKEN)) {
                --$nbOpenCurlyBrace;
                if (0 === $nbOpenCurlyBrace) {
                    break;
                }

                continue;
            }
        }
    }

    protected static function reset(): void
    {
        self::$buffer = [];
        self::$token = null;
        self::$previousToken = null;
        self::$previousVisibility = null;
        self::$nullable = false;
        self::$type = null;
        self::readToken();
        self::skip();
    }
}
