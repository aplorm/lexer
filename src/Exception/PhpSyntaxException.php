<?php

declare(strict_types=1);

namespace Orm\Lexer\Exception;

use Exception;

class PhpSyntaxException extends Exception
{
    private const CODE = 0X4C3;

    public function __construct(string $message = 'php syntax not valid')
    {
        parent::__construct($message, self::CODE);
    }
}
