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

/**
 * usefull token needed to analyse annotation.
 */
interface DocBlockTokenInterface
{
    public const AROBASE_TOKEN = '@';
    public const STAR_TOKEN = '*';
    public const SLASH_TOKEN = '/';
    public const ANTI_SLASH_TOKEN = '\\';
    public const EMPTY_TOKEN = '';
    public const SEMICOLON_TOKEN = ';';
    public const COLON_TOKEN = ':';
    public const COMMA_TOKEN = ',';
    public const OPEN_BRACKET_TOKEN = '[';
    public const CLOSE_BRACKET_TOKEN = ']';
    public const OPEN_CURLY_BRACE_TOKEN = '{';
    public const CLOSE_CURLY_BRACE_TOKEN = '}';
    public const OPEN_PARENTHESIS_TOKEN = '(';
    public const CLOSE_PARENTHESIS_TOKEN = ')';
    public const EQUAL_TOKEN = '=';
    public const DOLLAR_TOKEN = '$';
    public const PIPE_TOKEN = '|';
}
