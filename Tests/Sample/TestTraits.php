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

namespace Aplorm\Lexer\Tests\Sample;

trait TestTraits
{
    /**
     * @annotation6
     */
    private float $float = 1.5;
    private int $int = 1;
    private int $longInt = 1_000_000;
    private bool $boolean = true;
    private ?bool $nullable = null;


    public function mafunction4(
        string $param1,
        array $param2
    ): bool {
        if (true) {
        }

        return true;
    }

    public function mafunction2(string $param1 = 'bla', array $param2 = [['A' => 'B'], ['A', 'B'], 'A']): bool
    {
        if (true) {
        }

        return true;
    }

    public function mafunction3(string $param1 = <<<'EOT'
        bla bla
    EOT , array $param2 = [['A' => 'B'], ['A', 'B'], 'A']): bool
    {
        if (true) {
        }

        return true;
    }
}
