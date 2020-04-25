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

namespace Aplorm\Lexer\Tests\Lexer\Lexer;

use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Lexer\Lexer;
use Aplorm\Lexer\Tests\Sample\SampleClass;
use ReflectionClass;

class SuccessTest extends AbstractTest
{
    /**
     * function call in setUp function.
     */
    protected function doSetup(): void
    {
    }

    /**
     * function call in tearDown function.
     */
    protected function doTearDown(): void
    {
    }

    public static function setupBeforeClass(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
    }

    public function testLexer(): void
    {
        $reflectionClass = new ReflectionClass(SampleClass::class);
        /** @var string */
        $fileName = $reflectionClass->getFileName();

        Lexer::analyse($fileName);
        self::assertTrue(true);
    }
}
