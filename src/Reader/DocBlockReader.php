<?php


namespace Aplorm\Lexer\Reader;


use Aplorm\Lexer\DocBlock\AnnotationData;
use Aplorm\Lexer\Token\StringTokenJar;

class DocBlockReader
{
    private StringTokenJar $jar;

    /**
     * @var array<string, AnnotationData>
     */
    private array $annotations = [];

    private bool $parsed;

    public function __construct(StringTokenJar $jar)
    {
        $this->jar = $jar;
        $this->parsed = false;
    }

    public function getAnnotations(): array
    {
        if ($this->jar->isEmpty()) {
            return [];
        }

        return $this->read();
    }

    public function getAnnotation(string $annotationName): ?AnnotationData
    {
        if (!$this->parsed) {
            $this->read();
        }
        return $this->annotations[$annotationName] ?? null;
    }

    private function read()
    {
        if ($this->parsed) {
            return $this->annotations;
        }

        $token = $this->jar->get();
        do {
            if ('@' === $token) {
                $annotationData = $this->readAnnotation();
                if (!empty($annotationData)) {
                    $this->annotations[$annotationData['name']] = new AnnotationData($annotationData['name'], $annotationData['parameters']);
                }

                continue;
            }
        } while ($token = $this->jar->next());

        $this->parsed = true;

        return $this->annotations;
    }

    private function readAnnotation(): array
    {
        $buffer = $parameters= [];
        $token = $this->jar->next();
        do {
            if (in_array($token, ['('], true)) {
                $parameters = $this->readParameters();
                continue;
            }

            if(in_array($token, ['*'], true)) {
                return [
                    'name' => implode('', $buffer),
                    'parameters' => $parameters
                ];
            }

            $buffer[] = $token;
        } while($token = $this->jar->next());

        return [];
    }

    private function readParameters(): array
    {

        $parameters = [];
        $token = $this->jar->next();
        do {
            if (in_array($token, ['('], true)) {
                $parameters[] = $this->readParameter();
                $token = $this->jar->get();
            }

            if(in_array($token, [')'], true)) {
                return $parameters;
            }

        } while($token = $this->jar->next());

        return [];
    }

    private function readParameter()
    {
        $token = $this->jar->get();
        $buffer = [];
        do {
            if(in_array($token, [')'])) {
                return implode('', $buffer);
            }

            $buffer[] = $token;
        } while($token = $this->jar->next());

        return null;
    }
}
