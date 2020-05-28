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

use Aplorm\Lexer\Analyser\TokenAnalyser;
use Behat\Gherkin\Node\PyStringNode;
use PHPUnit\Framework\Assert;

trait CommonTrait
{
    use CoverageTrait;

    private array $analysedParts = [];

    private array $testedPart = [];

    /**
     * @Given the piece of code :
     */
    public function thePieceOfCode(PyStringNode $string): void
    {
        $tokens = $this->getTokenFrom($string);

        $this->analysedParts = TokenAnalyser::analyse($tokens);
    }

    /**
     * transform phpcode into token.
     *
     * @return mixed[]
     */
    private function getTokenFrom(PyStringNode $string): array
    {
        return token_get_all(trim((string) $string));
    }

    private function attributesAreNegative(string $attributes, array $in): void
    {
        if ('nothing' === $attributes) {
            return;
        }

        $keys = explode(',', $attributes);

        foreach ($keys as $key) {
            Assert::assertArrayHasKey($key, $in);
            Assert::assertFalse($in[$key]);
        }
    }

    private function attributesArePositiveOr(string $attributes, array $in): void
    {
        if ('nothing' === $attributes) {
            return;
        }

        $keys = explode(',', $attributes);

        foreach ($keys as $key) {
            if (false !== strstr($key, '=')) {
                [
                    $key,
                    $value
                ] = explode('=', $key);
            } else {
                $value = null;
            }

            Assert::assertArrayHasKey($key, $in);
            if (null !== $value) {
                Assert::assertEquals($value, $in[$key]);
            } else {
                Assert::assertTrue($in[$key]);
            }
        }
    }

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
}
