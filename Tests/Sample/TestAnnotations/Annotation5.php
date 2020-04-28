<?php

namespace Aplorm\Lexer\Tests\Sample\TestAnnotations;

class Annotation5
{
    public $data;

    public function __construct(int $data)
    {
        $this->data = $data;
    }
}
