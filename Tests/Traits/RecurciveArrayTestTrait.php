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

namespace Aplorm\Lexer\Tests\Traits;

trait RecurciveArrayTestTrait
{
    /**
     * [recurciveCheck description].
     *
     * @param mixed|null $expectedData
     * @param mixed|null $effectiveData
     */
    protected function recurciveCheck($expectedData, $effectiveData): void
    {
        if (!\is_array($expectedData) && !\is_array($effectiveData)) {
            $this->assertEquals($expectedData, $effectiveData, $key);

            return;
        }

        if (!\is_array($expectedData) || !\is_array($effectiveData)) {
            $this->assertFalse(true, 'Expected data and effectiveData are not the same type');

            return;
        }

        foreach ($expectedData as $key => $value) {
            if (!is_numeric($key)) {
                $this->assertArrayHasKey($key, $effectiveData);
            }
            if (!\is_array($value)) {
                $this->assertEquals($value, $effectiveData[$key] ?? null, (string) $key);

                continue;
            }

            $this->recurciveCheck($value, $effectiveData[$key] ?? []);
        }
    }
}
