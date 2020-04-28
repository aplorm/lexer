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
use Aplorm\Lexer\Exception\AnnotationSyntaxException;

/**
 * Analyse token extract from php file with token_get_all method.
 */
class TokenAnalyser
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
     * the current element type.
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
     */
    private static int $iterator = 0;

    /**
     * size of the token array.
     */
    private static int $tokenLength = 0;

    /**
     * parts find during analyzed.
     *
     * @var array<string, mixed>
     */
    private static array $parts = [
        LexedPartInterface::NAMESPACE_PART => null,
        LexedPartInterface::CLASS_NAME_PART => null,
        LexedPartInterface::CLASS_ALIASES_PART => [],
        LexedPartInterface::USE_PART => [],
        LexedPartInterface::VARIABLE_PART => [],
        LexedPartInterface::FUNCTION_PART => [],
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

            self::next();
        }

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
        $namespace = self::getSpecificPart(LexedPartInterface::NAMESPACE_PART);
        $fullyClassName = sprintf('%s\\%s', $namespace, $className);

        $classData = [
            'className' => $className,
            'namespace' => $namespace,
            'fullyQualifiedClassName' => $fullyClassName,
            'annotations' => self::$lastAnnotations,
        ];

        self::$lastAnnotations = null;

        self::addDataToPart(LexedPartInterface::CLASS_NAME_PART, null, $classData);
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
                    $fullNamespace = sprintf('%s%s', $namespaceBase, self::flush());
                } else {
                    $fullNamespace = self::flush();
                }
            } elseif (self::isA([
                TokenNameInterface::CLOSE_CURLY_BRACE_TOKEN,
                TokenNameInterface::COMMA_TOKEN,
            ])) {
                self::addNamespaceToPart($fullNamespace, $namespaceBase, $classNamespace);
                $fullNamespace = null;
                if (self::isA(TokenNameInterface::CLOSE_CURLY_BRACE_TOKEN)) {
                    $namespaceBase = null;
                }
            } else {
                self::buffering();
            }

            self::next();
        }
        self::addNamespaceToPart($fullNamespace, $namespaceBase, $classNamespace);
    }

    protected static function handleElement(): void
    {
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse(self::$lastAnnotations);
        if (null === $partType) {
            return;
        }

        self::addDataToPart($partType, $partName, $partData);
    }

    /**
     * Add namespace to analyser part.
     *
     * @param string|null $fullNamespace  the full namespace
     * @param string|null $baseNamespace  a base namespace for group use case
     * @param bool        $classNamespace handle classNamespace case
     */
    protected static function addNamespaceToPart(?string &$fullNamespace = null, ?string $baseNamespace = null, bool $classNamespace = false): void
    {
        if (null === $fullNamespace) {
            $alias = self::previousTokenValue();
            if (null !== $baseNamespace) {
                $fullNamespace = sprintf('%s%s', $baseNamespace, self::flush());
            } else {
                $fullNamespace = self::flush();
            }
        } else {
            $alias = self::flush();
        }

        if (empty($fullNamespace)) {
            return;
        }

        if ($classNamespace) {
            self::addDataToPart(LexedPartInterface::NAMESPACE_PART, null, $fullNamespace);
        } else {
            self::addDataToPart(LexedPartInterface::USE_PART, $fullNamespace);
            self::addDataToPart(LexedPartInterface::CLASS_ALIASES_PART, $alias, $fullNamespace);
        }
    }

    /**
     * Add data to specific part.
     *
     * @param string      $part  the target part
     * @param string|null $key   the part name as key
     * @param mixed       $value the value of part, true if not provided
     */
    protected static function addDataToPart(string $part, ?string $key, &$value = true): void
    {
        if (null === $key) {
            self::$parts[$part] = $value;

            return;
        }

        self::$parts[$part][$key] = $value;
    }

    /**
     * init analyser.
     *
     * @param array<mixed> $tokens the tokens to analyse
     *
     * @codeCoverageIgnore
     */
    protected static function init(array &$tokens): void
    {
        self::$buffer = [];
        self::$parts = [
            LexedPartInterface::NAMESPACE_PART => null,
            LexedPartInterface::CLASS_NAME_PART => null,
            LexedPartInterface::CLASS_ALIASES_PART => [],
            LexedPartInterface::USE_PART => [],
            LexedPartInterface::VARIABLE_PART => [],
            LexedPartInterface::FUNCTION_PART => [],
        ];
        self::$previousToken = null;
        self::$previousVisibility = null;
        self::$nullable = false;
        self::$type = null;
        self::$lastAnnotations = null;
        self::$token = self::$previousToken = null;
        self::$tokenLength = \count($tokens);
        self::$iterator = 0;
        self::$tokens = &$tokens;
        self::readToken();
        ClassPartAnalyser::init(self::$tokens, self::$iterator, self::$tokenLength);
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
}
