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

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class ClassContext implements Context
{
    use CommonTrait;

    /**
     * @Then I found the :arg1 :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function iFoundThe($arg1, $arg2): void
    {
        Assert::assertEquals($arg2, $this->analysedParts[$arg1]);
    }

    /**
     * @Then I found the :arg1 with :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theNamedWith($arg1, $arg2): void
    {
        Assert::assertArrayHasKey($arg1, $this->analysedParts);

        $this->attributesArePositiveOr($arg2, $this->analysedParts[$arg1]);
    }

    /**
     * @Then I found :arg3 :arg1, with :arg2
     *
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     */
    public function iFoundWith($arg1, $arg2, $arg3): void
    {
        Assert::assertCount((int) $arg3, $this->analysedParts[$arg1]);
        $this->attributesArePositiveOr($arg2, $this->analysedParts[$arg1]);
    }

    /**
     * @Then in class I found :arg2 :arg1
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function iFound($arg1, $arg2): void
    {
        Assert::assertCount((int) $arg2, $this->analysedParts[$arg1]);
    }

    /**
     * @Then the :arg1 :arg2, with :arg3
     *
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     */
    public function theWith($arg1, $arg2, $arg3): void
    {
        Assert::assertArrayHasKey($arg1, $this->analysedParts[$arg2]);
        $this->attributesArePositiveOr($arg3, $this->analysedParts[$arg2][$arg1]);
    }

    /**
     * @Then the :arg1 :arg2, without :arg3
     *
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     */
    public function theWithout($arg1, $arg2, $arg3): void
    {
        $this->attributesAreNegative($arg3, $this->analysedParts[$arg2][$arg1]);
    }

    /**
     * @Then the :arg1 :arg2, with :arg4 annotation named :arg3
     *
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     * @param string $arg4
     */
    public function theWithAnnotationNamed($arg1, $arg2, $arg3, $arg4): void
    {
        Assert::assertCount((int) $arg4, $this->analysedParts[$arg2][$arg1]['annotations']);
        Assert::assertEquals($arg3, $this->analysedParts[$arg2][$arg1]['annotations'][0]['name']);
    }
}
