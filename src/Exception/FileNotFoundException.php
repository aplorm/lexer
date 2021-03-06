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

namespace Aplorm\Lexer\Exception;

use Exception;

class FileNotFoundException extends Exception
{
    private const CODE = 0X4C1;

    public function __construct(string $file)
    {
        parent::__construct($file.' not found', self::CODE);
    }
}
