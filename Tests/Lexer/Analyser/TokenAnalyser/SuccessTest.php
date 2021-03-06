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

use Aplorm\Common\Lexer\LexedPartInterface;
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
        self::assertEquals($this->provideClassNamespace(NamespaceClassTest::class), $parts[LexedPartInterface::NAMESPACE_PART]);
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
        self::assertEquals($useNumber, \count($parts[LexedPartInterface::USE_PART]));
        self::assertTrue($parts[LexedPartInterface::USE_PART][$firstUse]);
    }

    /**
     * @dataProvider traitsProvider
     *
     * @param array<mixed> $tokens
     * @param string       $firstTrait
     * @param string       $fullyQualifiedTraitClass
     */
    public function testTraits(&$tokens, $firstTrait, $fullyQualifiedTraitClass): void
    {
        $parts = TokenAnalyser::analyse($tokens);

        self::assertArrayHasKey($firstTrait, $parts[LexedPartInterface::TRAITS_PART]);
        self::assertEquals($fullyQualifiedTraitClass, $parts[LexedPartInterface::TRAITS_PART][$firstTrait]);
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
        self::assertEquals($aliasNumber, \count($parts[LexedPartInterface::CLASS_ALIASES_PART]));
        self::assertArrayHasKey($firstAlias, $parts[LexedPartInterface::CLASS_ALIASES_PART]);
        self::assertEquals($aliasValue, $parts[LexedPartInterface::CLASS_ALIASES_PART][$firstAlias]);
    }

    /**
     * @dataProvider classExtendProvider
     *
     * @param array<mixed> $tokens
     */
    public function testExtends(&$tokens, ?string $extendValue): void
    {
        $parts = TokenAnalyser::analyse($tokens);
        self::assertEquals($extendValue, $parts[LexedPartInterface::CLASS_NAME_PART]['parent']);
    }
}
