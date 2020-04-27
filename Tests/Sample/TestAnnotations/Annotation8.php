<?php

namespace Aplorm\Lexer\Tests\Sample\TestAnnotations;

class Annotation8
{
    public $a;
    public $b;
    public $c;
    public $d;

    public function __construct(int $a, int $b, int $c, int $d)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
        $this->d = $d;
    }
}

