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
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    use CommonTrait;

    private array $testedPart = [];

    /**
     * @Then I found :arg2 :arg1
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function iFound($arg1, $arg2): void
    {
        Assert::assertCount((int) $arg2, $this->analysedParts[$arg1]);
        $this->testedPart = $this->analysedParts[$arg1];
    }

    /**
     * @Then the named :arg1, with :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theNamedWith($arg1, $arg2): void
    {
        Assert::assertArrayHasKey($arg1, $this->testedPart);

        $this->attributesArePositiveOr($arg2, $this->testedPart[$arg1]);
    }

    /**
     * @Then the named :arg1, without :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theNamedWithout($arg1, $arg2): void
    {
        Assert::assertArrayHasKey($arg1, $this->testedPart);

        $this->attributesAreNegative($arg2, $this->testedPart[$arg1]);
    }

    /**
     * @Then the named :arg1, has not nullable return :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theNamedHasNotNullableReturn($arg1, $arg2): void
    {
        Assert::assertArrayHasKey('returnType', $this->testedPart[$arg1]);
        Assert::assertEquals($arg2, $this->testedPart[$arg1]['returnType']['type']);
        Assert::assertFalse($this->testedPart[$arg1]['returnType']['nullable']);
    }

    /**
     * @Then the named :arg1, has nullable return :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theNamedHasNullableReturn($arg1, $arg2): void
    {
        Assert::assertArrayHasKey('returnType', $this->testedPart[$arg1]);
        Assert::assertEquals($arg2, $this->testedPart[$arg1]['returnType']['type']);
        Assert::assertTrue($this->testedPart[$arg1]['returnType']['nullable']);
    }

    /**
     * @Then the named :arg1 has :arg2 parameters
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theNamedHasParameters($arg1, $arg2): void
    {
        Assert::assertArrayHasKey($arg1, $this->testedPart);
        $this->testedPart = $this->testedPart[$arg1];
        Assert::assertCount((int) $arg2, $this->testedPart['parameters']);
    }

    /**
     * @Then the :arg1 parameters hasn't :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theParametersHasnt($arg1, $arg2): void
    {
        $this->attributesAreNegative($arg2, $this->testedPart['parameters'][$arg1]);
    }

    /**
     * @Then the :arg1 hasn't :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function hasnt($arg1, $arg2): void
    {
        $this->attributesAreNegative($arg2, $this->testedPart[$arg1]);
    }

    /**
     * @Then the :arg1 parameters has :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theParametersHas($arg1, $arg2): void
    {
        $this->attributesArePositiveOr($arg2, $this->testedPart['parameters'][$arg1]);
    }

    /**
     * @Then the :arg1 has :arg2
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function has($arg1, $arg2): void
    {
        $this->attributesArePositiveOr($arg2, $this->testedPart[$arg1]);
    }

    /**
     * @Then the :arg1 parameters with :arg2 has default value
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theParametersWithHasDefaultValue($arg1, $arg2): void
    {
        Assert::assertEquals($arg2, $this->testedPart['parameters'][$arg1]['value']);
    }

    /**
     * @Then the :arg1 with :arg2 has default value
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theWithHasDefaultValue($arg1, $arg2): void
    {
        Assert::assertEquals($arg2, $this->testedPart[$arg1]['value']);
    }

    /**
     * @Then the :arg1 parameters with array value
     *
     * @param string $arg1
     */
    public function theParametersWithArrayValue($arg1, PyStringNode $string): void
    {
        Assert::assertEquals($this->normalizeValue((string) $string), $this->testedPart['parameters'][$arg1]['value']);
    }

    /**
     * @Then the :arg1 with array value
     *
     * @param string $arg1
     */
    public function theWithArrayValue($arg1, PyStringNode $string): void
    {
        Assert::assertEquals($this->normalizeValue((string) $string), $this->testedPart[$arg1]['value']);
    }

    /**
     * @Then the named :arg1 will return :arg2 with nullable is :arg3
     *
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     */
    public function theNamedWillReturnWithNullableIs($arg1, $arg2, $arg3): void
    {
        Assert::assertEquals($arg2, $this->testedPart[$arg1]['returnType']['type']);
        Assert::assertEquals('true' === $arg3, $this->testedPart[$arg1]['returnType']['nullable']);
    }
}
