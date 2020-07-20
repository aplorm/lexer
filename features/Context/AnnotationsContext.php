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

use Aplorm\Lexer\Reader\DocBlockReader;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Behat\Tester\Exception\PendingException;
use Aplorm\Lexer\Token\StringTokenJar;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

/**
 * Defines application features from the specific context.
 */
class AnnotationsContext implements Context
{
    private static StringTokenJar $tokenJar;

    /**
     * @Transform /^NULL$/i
     */
    public function transformNull($null)
    {
        return null;
    }

    private function jsonToArray(string $string): ?array
    {
        return json_decode(trim($string), true);
    }

    /**
     * @Given nothing
     */
    public function nothing()
    {
        self::$tokenJar = new StringTokenJar();
    }

    /**
     * @Then the StringTokenJar object should contains :arg1 characters
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
        self::$tokenJar = new StringTokenJar(trim((string) $string));
    }

    /**
     * @Then the StringTokenJar object should contains :arg1 for the first token
     */
    public function theTokenJarObjectShouldContainsForTheFirstToken(?string $arg1)
    {
        Assert::assertEquals($arg1, self::$tokenJar->get());
    }

    /**
     * @When calling next function, the response must be :arg1
     */
    public function callingNextFunctionTheResponseMustBeNull(?string $arg1)
    {
        Assert::assertEquals($arg1, self::$tokenJar->next());
    }

    /**
     * @Then the DocBlock find array
     */
    public function theDocBlockFind(PyStringNode $node)
    {
        $result = $this->jsonToArray((string) $node);
        $docBlockReader = new DocBlockReader(self::$tokenJar);
        Assert::assertEquals($result, array_keys($docBlockReader->getAnnotations()));
    }

    /**
     * @Then the :arg1 has the next array has parameter
     */
    public function theHasTheNextArrayHasParameter($arg1, PyStringNode $node)
    {
        $result = $this->jsonToArray((string) $node);
        $docBlockReader = new DocBlockReader(self::$tokenJar);
        Assert::assertEquals($result, $docBlockReader->getAnnotation($arg1)->getParameters());
    }

    /**
     * @Then the :arg1 has the next array no parameter
     */
    public function theHasTheNextArrayNoParameter($arg1)
    {
        $docBlockReader = new DocBlockReader(self::$tokenJar);
        Assert::assertNull($docBlockReader->getAnnotation($arg1)->getParameters());
    }
}
