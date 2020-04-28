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

use Aplorm\Lexer\Exception\FileNotFoundException;

/**
 * class comment.
 *
 * @annotation
 * @annotation2({
 *     "key.1": 1,
 *     "key.2": "string",
 *     "key.3": SampleClass::constant,
 *     "key.4": @annotation3(@annotation11({1, 2, 3, 4})),
 *     "key.5": true
 * })
 * @annotation4("param")
 * @annotation5(1)
 * @annotation6(true)
 * @annotation7(SampleClass::constant)
 * @annotation8(1, 2, 3, 4)
 */
class SampleClass extends DummyClass implements DummyInterface, FooInterface
{
    public const A_CONSTANT = 'A_CONSTANT VALUE';

    public static $astatic = 1;

    /**
     * @annotation6
     */
    private float $float = 1.5;
    private int $int = 1;
    private int $longInt = 1_000_000;
    private bool $boolean = true;
    private ?bool $nullable = null;

    // line comment
    private string $string = 'une string avec des espaces';

    public $docBloc = <<<'EOD'
/**
 *  @annotation(namedParameter = 'test')
 */
EOD;

    public $eot = <<<'EOT'
/**
 *  @annotation(namedParameter = 'test')
 */
EOT;

    public $foobar = <<<'FOOBAR'
/**
 *  @annotation(namedParameter = 'test')
 */
FOOBAR;

    // bloc simple comment
    private ?FileNotFoundException $class;

    private array $array = [
        ['A' => 'B'],
        ['A', 'B'],
        'A',
    ];

    private array $array2 = [
        ['A' => 'B'],
        ['A', 'B'],
        'A',
    ];

    /**
     * [mafunction description].
     *
     * @param string $param1 [description]
     * @param array  $param2 [description]
     *
     * @return [type] [description]
     *
     * @annotationValid
     */
    public function mafunction(string $param1, array $param2): bool
    {
        if (true) {
        }

        return true;
    }

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
