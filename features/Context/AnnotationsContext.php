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

use Aplorm\Lexer\Analyser\DocBlockAnalyser;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class AnnotationsContext implements Context
{
    use CoverageTrait;

    private array $analysedParts = [];

    private array $testedPart = [];

    /**
     * Transform a json_encoded value into php value.
     *
     * @param mixed $value the value given in feature
     *
     * @return mixed the value normalized by json_decode
     */
    private function normalizeValue($value)
    {
        return json_decode(trim($value), true);
    }

    /**
     * @Given the piece of comment :
     */
    public function thePieceOfComment(PyStringNode $string): void
    {
        $this->analysedParts = DocBlockAnalyser::analyse((string) $string);
    }

    /**
     * @Then I found :arg1 annotation
     *
     * @param string $arg1
     */
    public function iFoundAnnotation($arg1): void
    {
        Assert::assertCount((int) $arg1, $this->analysedParts);
    }

    /**
     * @Then the :arg2 is named :arg1
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theIsNamed($arg1, $arg2): void
    {
        Assert::assertEquals($arg1, $this->analysedParts[(int) $arg2 - 1]['name']);
    }

    /**
     * @Then with :arg1 parameter in the :arg2 annotation
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function withParameter($arg1, $arg2): void
    {
        Assert::assertCount((int) $arg1, $this->analysedParts[(int) $arg2 - 1]['params']);
    }

    /**
     * @Then the :arg2 parameter with :arg1 has value in the :arg3 annotation
     *
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     */
    public function theParameterWithHasValue($arg1, $arg2, $arg3): void
    {
        Assert::assertEquals($arg1, $this->analysedParts[(int) $arg3 - 1]['params'][(int) $arg2 - 1]['value']);
    }

    /**
     * @Then the :arg1 parameter of the :arg2 annotation with array value:
     *
     * @param string $arg1
     * @param string $arg2
     */
    public function theParameterOfTheAnnotationWithArrayValue($arg1, $arg2, PyStringNode $string): void
    {
        Assert::assertEquals($this->normalizeValue((string) $string), $this->analysedParts[(int) $arg2 - 1]['params'][(int) $arg1 - 1]['value']);
    }

    /**
     * @Then the :arg3 parameter with :arg1 has value and :arg2 has name in the :arg4 annotation
     *
     * @param string $arg1
     * @param string $arg2
     * @param string $arg3
     * @param string $arg4
     */
    public function theParameterWithHasValueAndHasNameInTheAnnotation($arg1, $arg2, $arg3, $arg4): void
    {
        Assert::assertEquals($arg1, $this->analysedParts[(int) $arg4 - 1]['params'][(int) $arg3 - 1]['value']);
        Assert::assertEquals($arg2, $this->analysedParts[(int) $arg4 - 1]['params'][(int) $arg3 - 1]['name']);
    }
}
