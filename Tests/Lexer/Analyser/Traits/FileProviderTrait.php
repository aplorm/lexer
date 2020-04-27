<?php
/**
 *  This file is part of the Aplorm package.
 *
 *  (c) Nicolas Moral <n.moral@live.fr>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Aplorm\Lexer\Tests\Lexer\Analyser\Traits;

use ReflectionClass;
use function token_get_all;

trait FileProviderTrait
{
    protected function provideClassContent(string $className): string
    {
        $reflectionClass = new ReflectionClass($className);
        /** @var string */
        $fileName = $reflectionClass->getFileName();

        if (!file_exists($fileName)) {
            throw new \RuntimeException('File does not exists');
        }
        $content = file_get_contents($fileName);

        if (false === $fileName) {
            throw new \RuntimeException('Unable to open file');
        }

        return $content;
    }

    protected function tokeniseClass(string $className): array
    {
        $content = $this->provideClassContent($className);

        return token_get_all($content);
    }

    protected function provideClassNamespace(string $className): string
    {
        $reflectionClass = new ReflectionClass($className);
        // @var string
        return $reflectionClass->getNamespaceName();
    }
}
