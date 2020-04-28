<?php

namespace Aplorm\Lexer\Tests\Sample\TestAnnotations;

class Annotation11
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
