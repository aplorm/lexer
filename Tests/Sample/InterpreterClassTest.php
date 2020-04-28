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

use Aplorm\Lexer\Exception\ClassNotFoundException;
use Aplorm\Lexer\Tests\Sample\SampleClass;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation11;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation2;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation3;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation4;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation5;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation6;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation7;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation8;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation9;
use Aplorm\Lexer\Tests\Sample\TestAnnotations\Annotation;

/**
 * class comment.
 *
 * @Annotation
 * @Annotation2({
 *     "key.1": 1,
 *     "key.2": "string",
 *     "key.3": SampleClass::A_CONSTANT,
 *     "key.4": @Annotation3(@Annotation11({1, 2, 3, 4})),
 *     "key.5": true
 * })
 * @Annotation4("param")
 * @Annotation5(1)
 * @Annotation6(true)
 * @Annotation7(self::A_CONSTANT)
 * @Annotation7(SampleClass::A_CONSTANT)
 * @Annotation8(1, 2, 3, 4)
 */
class InterpreterClassTest extends DummyClass implements DummyInterface, FooInterface
{
    public const A_CONSTANT = 'self constant';

    public static $astatic = 1;

    /**
     * @Annotation6
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
    private ?ClassNotFoundException $class;

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
     * @return int|string
     *
     * @Annotation9(name="bla")
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
