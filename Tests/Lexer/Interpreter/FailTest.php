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

namespace Aplorm\Lexer\Tests\Lexer\Interpreter;

use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Exception\FileNotFoundException;
use Aplorm\Lexer\Lexer\Lexer;

class FailTest extends AbstractTest
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
        $this->expectException(FileNotFoundException::class);

        Lexer::analyse('dummyFile.php');
    }
}
