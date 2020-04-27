<?php

namespace Aplorm\Lexer\Tests\Sample\TestAnnotations;

class Annotation4
{
    public $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }
}
