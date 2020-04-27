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

namespace Aplorm\Lexer;

interface TypeInterface
{
    public const STRING_TYPE = 1;
    public const CLASS_CONSTANT_TYPE = 2;
    public const OTHER_CONSTANT_TYPE = 3;
    public const NUMBER_CONSTANT_TYPE = 4;
    public const ARRAY_TYPE = 5;
    public const OBJECT_TYPE = 6;
    public const ANNOTATION_TYPE = 7;
}
