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

interface TokenNameInterface
{
    public const NAMESPACE_TOKEN = T_NAMESPACE;
    public const USE_TOKEN = T_USE;
    public const AS_TOKEN = T_AS;

    public const WHITESPACE_TOKEN = T_WHITESPACE;
    public const STRING_TOKEN = T_STRING;
    public const NS_SEPARATOR_TOKEN = T_NS_SEPARATOR;

    public const DOC_COMMENT_TOKEN = T_DOC_COMMENT;

    public const PRIVATE_TOKEN = T_PRIVATE;
    public const PROTECTED_TOKEN = T_PROTECTED;
    public const PUBLIC_TOKEN = T_PUBLIC;
    public const VARIABLE_TOKEN = T_VARIABLE;
    public const FUNCTION_TOKEN = T_FUNCTION;
    public const CONSTANT_TOKEN = T_CONST;

    public const CLASS_TOKEN = T_CLASS;
    public const EXTENDS_TOKEN = T_EXTENDS;
    public const IMPLEMENTS_TOKEN = T_IMPLEMENTS;

    public const SEMI_COLON_TOKEN = ';';
    public const OPEN_CURLY_BRACE_TOKEN = '{';
    public const CLOSE_CURLY_BRACE_TOKEN = '}';
    public const COMMA_TOKEN = ',';
    public const EMPTY_TOKEN = '';
    public const QUESTION_MARK_TOKEN = '?';

    public const VISIBILITY_TOKENS = [
        self::PRIVATE_TOKEN,
        self::PROTECTED_TOKEN,
        self::PUBLIC_TOKEN,
    ];

    public const CUSTOM_TOKEN = [
        self::SEMI_COLON_TOKEN => self::SEMI_COLON_TOKEN,
        self::OPEN_CURLY_BRACE_TOKEN => self::OPEN_CURLY_BRACE_TOKEN,
        self::CLOSE_CURLY_BRACE_TOKEN => self::CLOSE_CURLY_BRACE_TOKEN,
        self::COMMA_TOKEN => self::COMMA_TOKEN,
        self::EMPTY_TOKEN => self::EMPTY_TOKEN,
        self::QUESTION_MARK_TOKEN => self::QUESTION_MARK_TOKEN,
    ];
}
