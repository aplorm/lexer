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

namespace Aplorm\Lexer\Tests\Lexer\Analyser\Traits;

use Aplorm\Lexer\Tests\Sample\DummyClass as TraitDummyClass;
use Aplorm\Lexer\Tests\Sample\GroupUseClassTest;
use Aplorm\Lexer\Tests\Sample\NamespaceClassTest;
use Aplorm\Lexer\Tests\Sample\SubNamespace\DummyClass;
use Aplorm\Lexer\Tests\Sample\SubNamespace\FooClass;
use Aplorm\Lexer\Tests\Sample\UseAsClassTest;

trait FileDataProviderTrait
{
    use FileProviderTrait;

    /**
     * @return array<array<mixed>>
     */
    public function useProvider(): array
    {
        return [
            [
                $this->tokeniseClass(NamespaceClassTest::class),
                DummyClass::class,
                1,
            ],
            [
                $this->tokeniseClass(GroupUseClassTest::class),
                DummyClass::class,
                2,
            ],
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function traitsProvider(): array
    {
        return [
            [
                $this->tokeniseClass(TraitDummyClass::class),
                'TestTraits',
            ],
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function useAsProvider(): array
    {
        return [
            [
                $this->tokeniseClass(UseAsClassTest::class),
                'DC',
                DummyClass::class,
                1,
            ],
            [
                $this->tokeniseClass(GroupUseClassTest::class),
                'FC',
                FooClass::class,
                2,
            ],
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function classExtendProvider(): array
    {
        return [
            [
                $this->tokeniseClass(UseAsClassTest::class),
                NULL,
            ],
            [
                $this->tokeniseClass(GroupUseClassTest::class),
                'DummyClass',
            ],
        ];
    }
}
