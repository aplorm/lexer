<?php

namespace Aplorm\Lexer\Tests\Sample\TestAnnotations;

class Annotation6
{
    public $data;

    public function __construct(bool $data)
    {
        $this->data = $data;
    }
}
