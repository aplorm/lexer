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

namespace Aplorm\Lexer\Tests\Sample;

use Aplorm\Lexer\Tests\Sample\SubNamespace\ {
    DummyClass,
    FooClass as FC
};

class GroupUseClassTest extends DummyClass
{
    private DummyClass $dc;
    private FC $dc2;
}
