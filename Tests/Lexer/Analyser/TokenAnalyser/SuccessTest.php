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

namespace Aplorm\Lexer\Tests\Lexer\Analyser\TokenAnalyser;

use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Analyser\TokenAnalyser;
use Aplorm\Lexer\Tests\Lexer\Analyser\Traits\FileDataProviderTrait;
use Aplorm\Lexer\Tests\Sample\NamespaceClassTest;

class SuccessTest extends AbstractTest
{
    use FileDataProviderTrait;

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

    public function testNamespace(): void
    {
        $tokens = $this->tokeniseClass(NamespaceClassTest::class);
        $parts = TokenAnalyser::analyse($tokens);
        self::assertEquals($this->provideClassNamespace(NamespaceClassTest::class), $parts[TokenAnalyser::NAMESPACE_PART]);
    }

    /**
     * @dataProvider useProvider
     *
     * @param array<mixed> $tokens
     * @param string       $firstUse
     * @param int          $useNumber
     */
    public function testUse(&$tokens, $firstUse, $useNumber): void
    {
        $parts = TokenAnalyser::analyse($tokens);

        self::assertEquals($useNumber, \count($parts[TokenAnalyser::USE_PART]));
        self::assertTrue($parts[TokenAnalyser::USE_PART][$firstUse]);
    }

    /**
     * @dataProvider useAsProvider
     *
     * @param array<mixed> $tokens
     * @param string       $firstAlias
     * @param string       $aliasValue
     * @param int          $aliasNumber
     */
    public function testUseAs(&$tokens, $firstAlias, $aliasValue, $aliasNumber): void
    {
        $parts = TokenAnalyser::analyse($tokens);
        self::assertEquals($aliasNumber, \count($parts[TokenAnalyser::CLASS_ALIASES]));
        self::assertArrayHasKey($firstAlias, $parts[TokenAnalyser::CLASS_ALIASES]);
        self::assertEquals($aliasValue, $parts[TokenAnalyser::CLASS_ALIASES][$firstAlias]);
    }
}
