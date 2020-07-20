<?php


namespace Aplorm\Lexer\DocBlock;


class AnnotationData
{
    private string $name;

    private ?array $parameters;

    public function __construct(string $name, ?array $parameters = null)
    {
        $this->name = $name;
        if(empty($parameters)) {
            $this->parameters = null;
        } else {
            $this->parameters = $parameters;
        }
    }

    public function getParameters(): ?array
    {
        return $this->parameters;
    }
}
