<?php

declare(strict_types=1);

namespace Orm\Lexer\Exception;

use Exception;

class ClassNotFoundException extends Exception
{
    private const CODE = 0X4C1;

    public function __construct(string $class)
    {
        parent::__construct($class.' not found', self::CODE);
    }
}
