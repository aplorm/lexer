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

namespace Aplorm\Lexer\Behat\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Tester\Exception\PendingException;
use Aplorm\Lexer\Token\TokenJar;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class AnnotationsContext implements Context
{
    use CoverageTrait;

    private static TokenJar $tokenJar;

    /**
     * @Given nothing
     */
    public function nothing()
    {
        self::$tokenJar = new TokenJar();
    }

    /**
     * @Then the TokenJar object should contains :arg1 characters
     */
    public function theTokenjarObjectShouldContainsCharacters($arg1)
    {
        Assert::assertEquals($arg1, self::$tokenJar->count());
    }

    /**
     * @Given a docblock:
     */
    public function aDocblock(PyStringNode $string)
    {
        self::$tokenJar = new TokenJar(trim((string) $string));
    }
}
