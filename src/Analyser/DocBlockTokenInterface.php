<?php

declare(strict_types=1);

namespace Orm\Lexer\Analyser;

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
}
