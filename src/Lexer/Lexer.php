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

namespace Aplorm\Lexer\Lexer;

use Aplorm\Lexer\Analyser\TokenAnalyser;
use Aplorm\Lexer\Exception\FileCorruptedException;
use Aplorm\Lexer\Exception\FileNotFoundException;
use function token_get_all;

class Lexer
{
    protected static ?string $classContent = null;

    /**
     * analyse class.
     *
     * @param string $filePath the file to analyse
     */
    public static function &analyse(string $filePath): array
    {
        self::fileExist($filePath);
        self::getFileContent($filePath);

        $tokens = token_get_all(self::$classContent);

        $parts = &TokenAnalyser::analyse($tokens);

        return $parts;
    }

    /**
     * test if class exist.
     *
     * @throws FileNotFoundException if class does not exists
     */
    private static function fileExist(string $filePath): void
    {
        if (file_exists($filePath)) {
            return;
        }

        throw new FileNotFoundException($filePath);
    }

    /**
     * get file content.
     *
     * @param string $filePath the file to analyse
     *
     * @throws FileCorruptedException if file_get_contents return false
     *
     * @return string the file content
     */
    private static function getFileContent(string $filePath): string
    {
        if (null === self::$classContent) {
            $content = file_get_contents($filePath);

            if (false === $content) {
                throw new FileCorruptedException($filePath);
            }

            self::$classContent = &$content;
        }

        return self::$classContent;
    }
}
