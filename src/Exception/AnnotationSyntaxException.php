<?php

declare(strict_types=1);

namespace Orm\Lexer\Exception;

use Exception;

class AnnotationSyntaxException extends Exception
{
    private const CODE = 0X4C2;

    public function __construct(string $message = 'comment syntax not valid')
    {
        parent::__construct($message, self::CODE);
    }
}
