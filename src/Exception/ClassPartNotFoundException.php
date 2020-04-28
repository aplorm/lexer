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

class ClassPartNotFoundException extends Exception
{
    private const CODE = 0X4C3;

    public function __construct(string $partName)
    {
        parent::__construct($partName.' not found', self::CODE);
    }
}
