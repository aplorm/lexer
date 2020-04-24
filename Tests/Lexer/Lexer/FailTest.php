<?php

declare(strict_types=1);

namespace Orm\Lexer\Tests\Lexer\Lexer;

use Orm\Common\Test\AbstractTest;
use Orm\Lexer\Exception\ClassNotFoundException;
use Orm\Lexer\Lexer\Lexer;

class FailTest extends AbstractTest
{
    /**
     * function call in setUp function.
     */
    protected function doSetup()
    {
    }

    /**
     * function call in tearDown function.
     */
    protected function doTearDown()
    {
    }

    public static function setupBeforeClass(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
    }

    public function testLexer()
    {
        $this->expectException(ClassNotFoundException::class);

        $lexer = Lexer::analyse('\\Dummy\\Class');
    }
}
