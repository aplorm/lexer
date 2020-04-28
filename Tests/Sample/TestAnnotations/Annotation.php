<?php

namespace Aplorm\Lexer\Tests\Sample\TestAnnotations;

class Annotation
{
}

class Annotation2
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
}

class Annotation3
{
    public $data;

    public function __construct(Annotation11 $data)
    {
        $this->data = $data;
    }
}

class Annotation11
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }
}

class Annotation4
{
    public $data;

    public function __construct(string $data)
    {
        $this->data = $data;
    }
}

class Annotation5
{
    public $data;

    public function __construct(int $data)
    {
        $this->data = $data;
    }
}

class Annotation6
{
    public $data;

    public function __construct(bool $data)
    {
        $this->data = $data;
    }
}

class Annotation7
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}

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

class Annotation9
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
}
