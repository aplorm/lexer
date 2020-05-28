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
    public const OPEN_TAG_TOKEN = T_OPEN_TAG;
    public const DECLARE_TOKEN = T_DECLARE;

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
    public const STATIC_TOKEN = T_STATIC;

    public const CLASS_TOKEN = T_CLASS;
    public const TRAIT_TOKEN = T_TRAIT;
    public const EXTENDS_TOKEN = T_EXTENDS;
    public const IMPLEMENTS_TOKEN = T_IMPLEMENTS;

    public const DOUBLE_ARROW_TOKEN = T_DOUBLE_ARROW;
    public const DOUBLE_COLON_TOKEN = T_DOUBLE_COLON;
    public const SEMI_COLON_TOKEN = ';';
    public const COLON_TOKEN = ':';
    public const OPEN_CURLY_BRACE_TOKEN = '{';
    public const CLOSE_CURLY_BRACE_TOKEN = '}';
    public const OPEN_BRACKET_TOKEN = '[';
    public const CLOSE_BRACKET_TOKEN = ']';
    public const OPEN_PARENTHESIS_TOKEN = '(';
    public const CLOSE_PARENTHESIS_TOKEN = ')';
    public const COMMA_TOKEN = ',';
    public const EMPTY_TOKEN = '';
    public const QUESTION_MARK_TOKEN = '?';
    public const EQUAL_TOKEN = '=';
    public const AND_TOKEN = '&';

    public const CONSTANT_ENCAPSED_STRING_TOKEN = T_CONSTANT_ENCAPSED_STRING;
    public const ENCAPSED_AND_WHITESPACE_TOKEN = T_ENCAPSED_AND_WHITESPACE;
    public const LNUMBER_TOKEN = T_LNUMBER;
    public const DNUMBER_TOKEN = T_DNUMBER;
    public const ARRAY_TOKEN = T_ARRAY;

    public const VISIBILITY_TOKENS = [
        self::PRIVATE_TOKEN,
        self::PROTECTED_TOKEN,
        self::PUBLIC_TOKEN,
    ];

    public const VALUE_TOKENS = [
        self::CONSTANT_ENCAPSED_STRING_TOKEN,
        self::ENCAPSED_AND_WHITESPACE_TOKEN,
        self::LNUMBER_TOKEN,
        self::DNUMBER_TOKEN,
    ];

    public const OPEN_ARRAY_TOKENS = [
        self::ARRAY_TOKEN,
        self::OPEN_BRACKET_TOKEN,
    ];

    public const CLOSE_ARRAY_TOKENS = [
        self::CLOSE_PARENTHESIS_TOKEN,
        self::CLOSE_BRACKET_TOKEN,
    ];

    public const SKIPPED_TOKENS = [
        self::WHITESPACE_TOKEN,
        self::EMPTY_TOKEN,
    ];

    public const CUSTOM_TOKENS = [
        self::SEMI_COLON_TOKEN => self::SEMI_COLON_TOKEN,
        self::OPEN_CURLY_BRACE_TOKEN => self::OPEN_CURLY_BRACE_TOKEN,
        self::CLOSE_CURLY_BRACE_TOKEN => self::CLOSE_CURLY_BRACE_TOKEN,
        self::COMMA_TOKEN => self::COMMA_TOKEN,
        self::EMPTY_TOKEN => self::EMPTY_TOKEN,
        self::QUESTION_MARK_TOKEN => self::QUESTION_MARK_TOKEN,
        self::OPEN_BRACKET_TOKEN => self::OPEN_BRACKET_TOKEN,
        self::CLOSE_BRACKET_TOKEN => self::CLOSE_BRACKET_TOKEN,
        self::OPEN_PARENTHESIS_TOKEN => self::OPEN_PARENTHESIS_TOKEN,
        self::CLOSE_PARENTHESIS_TOKEN => self::CLOSE_PARENTHESIS_TOKEN,
        self::EQUAL_TOKEN => self::EQUAL_TOKEN,
        self::COLON_TOKEN => self::COLON_TOKEN,
        self::AND_TOKEN => self::AND_TOKEN,
    ];
}
