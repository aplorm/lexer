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

namespace Aplorm\Lexer\Analyser\Traits;

use Aplorm\Lexer\Analyser\TokenNameInterface;

trait CommonAnalyserTraits
{
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

    protected static function cleanStringValue(string $value): string
    {
        if (\in_array($value[0], ['\'', '"'], true)) {
            return trim($value, '\'"');
        }

        return $value;
    }

    /**
     * skip all the jumpable token.
     *
     * @return bool if a skip is made return true, false otherwise
     */
    protected static function skip(): bool
    {
        $skip = false;
        while (self::$iterator < self::$tokenLength && self::isA(
            [
                TokenNameInterface::WHITESPACE_TOKEN,
                TokenNameInterface::EMPTY_TOKEN,
                TokenNameInterface::OPEN_TAG_TOKEN,
            ]
        )) {
            self::next();
            $skip = true;
        }

        return $skip;
    }

    /**
     * skip until the token is find.
     *
     * @param int|int[]|string|string[] $tokens the expected type
     *
     * @return bool if a skip is made return true, false otherwise
     */
    protected static function skipUntil($tokens): bool
    {
        $skip = false;
        while (self::$iterator < self::$tokenLength && !self::isA($tokens)) {
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
     * read token and normalyse custom token.
     */
    protected static function readToken(): void
    {
        if (!isset(self::$tokens[self::$iterator])) {
            return;
        }

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
     * get custom token value.
     *
     * @param string $value the unedefined value
     *
     * @return string|null the token find with the value
     */
    protected static function getCustomToken(string &$value): ?string
    {
        return TokenNameInterface::CUSTOM_TOKENS[$value] ?? null;
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

    /**
     * @codeCoverageIgnore
     */
    protected static function debug(): void
    {
        if (\is_int(self::$token[0])) {
            echo self::$token[0], ' ', token_name(self::$token[0]), ' ', self::$token[1], '' , PHP_EOL;
        } else {
            echo self::$token[0], ' ', 'CUSTOM_TOKEN', ' ', self::$token[1], '' , PHP_EOL;
        }
    }
}
