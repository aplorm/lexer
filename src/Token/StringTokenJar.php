<?php

namespace Aplorm\Lexer\Token;

use const \PHP_EOL;

class StringTokenJar
{
    private array $tokens = [];

    private int $position = 0;

    private ?int $count = null;

    public function __construct(string $docBlock = null)
    {
        if (!empty($docBlock)) {
            $this->tokens = $this->tokenize($docBlock);
        }
    }

    public function isEmpty()
    {
        return 0 === $this->count();
    }

    public function count(): int
    {
        if (null !== $this->count) {
            return $this->count;
        }

        $this->count = count($this->tokens);

        return $this->count;
    }

    public function get(): ?string
    {
        return $this->tokens[$this->position] ?? null;
    }

    public function next()
    {
        if ($this->position + 1 >= $this->count()) {
            return null;
        }

        ++$this->position;

        return $this->get();
    }

    private function tokenize(string $docBlock): array
    {
        $tokens = str_split($docBlock);

        return array_values(array_filter($tokens, function (string $token){
           return PHP_EOL !== $token;
        }));
    }
}
