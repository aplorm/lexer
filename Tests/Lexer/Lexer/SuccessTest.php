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

use Aplorm\Common\Lexer\LexedPartInterface;
use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Lexer\Lexer;
use Generator;

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

    /**
     * @dataProvider useAsProvider
     *
     * @param string $fileName
     * @param bool   $isTrait
     */
    public function testLexer($fileName, $isTrait): void
    {
        $parts = Lexer::analyse($fileName);
        self::assertTrue(true);
        self::assertEquals($isTrait, $parts[LexedPartInterface::CLASS_NAME_PART]['isTrait']);
    }

    /**
     * @return Generator<array<mixed>>
     */
    public function useAsProvider(): Generator
    {
        if (isset($_SERVER['TRAVIS_BUILD_DIR'])) {
            $dir = $_SERVER['TRAVIS_BUILD_DIR'].'/'.$_ENV['SAMPLE_CLASSES'];
        } else {
            $dir = $_ENV['PWD'].'/'.$_ENV['SAMPLE_CLASSES'];
        }

        yield [
            $dir.'/SampleClass.php',
            false,
        ];

        yield [
            $dir.'/TestTraits.php',
            true,
        ];
    }
}
