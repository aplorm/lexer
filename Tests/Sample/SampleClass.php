<?php

declare(strict_types=1);

namespace Orm\Lexer\Tests\Sample;

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
    public const A_CONSTANT = 1;

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
    private string $string = "une string avec des espaces";

    public $docBloc = <<<EOD
/**
 *  @annotation(namedParameter = 'test')
 */
EOD;

    public $eot = <<<EOT
/**
 *  @annotation(namedParameter = 'test')
 */
EOT;

    public $foobar = <<<FOOBAR
/**
 *  @annotation(namedParameter = 'test')
 */
FOOBAR;

    // bloc simple comment
    private ?ClassNotFoundException $class;

    public function mafunction($param1, $param2): bool
    {
        if (true) {
        }

        return true;
    }
}
