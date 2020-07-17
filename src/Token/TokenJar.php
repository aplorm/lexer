<?php

namespace Aplorm\Lexer\Token;

class TokenJar
{
    private array $token = [];

    public function __construct(string $docBlock = null)
    {
        if (!empty($docBlock)) {
            $this->token = $this->tokenise($docBlock);
        }
    }

    public function count(): int
    {
        return count($this->token);
    }

    private function tokenise(string $docBlock): array
    {
        $tokens = str_split($docBlock);

        return array_filter($tokens, function (string $token){
           return \PHP_EOL !== $token;
        });
    }
}
