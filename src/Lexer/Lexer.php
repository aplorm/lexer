<?php

declare(strict_types=1);

namespace Orm\Lexer\Lexer;

use Orm\Lexer\Analyser\TokenAnalyser;
use Orm\Lexer\Exception\ClassNotFoundException;
use ReflectionClass;

class Lexer
{
    protected static ?string $class = null;

    protected static ?ReflectionClass $reflector = null;

    protected static ?string $classContent = null;

    /**
     * analyse class.
     *
     * @param string $class the analyzed class
     */
    public static function analyse(string $class)
    {
        self::classExist($class);
        self::$class = $class;

        self::getFileContent();

        $tokens = token_get_all(self::$classContent);

        TokenAnalyser::analyse($tokens);
    }

    /**
     * test if class exist.
     *
     * @throws ClassNotFoundException if class does not exists
     */
    private static function classExist(string $class): void
    {
        if (class_exists($class)) {
            return;
        }

        throw new ClassNotFoundException($class);
    }

    /**
     * return the file path for a specifi class.
     */
    private static function getFilePath(): string
    {
        if (null === self::$reflector) {
            self::$reflector = new ReflectionClass(self::$class);
        }

        return self::$reflector->getFileName();
    }

    private static function getFileContent()
    {
        if (null === self::$classContent) {
            self::$classContent = file_get_contents(self::getFilePath());
        }

        return self::$classContent;
    }
}
