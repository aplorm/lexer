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

namespace Aplorm\Lexer\Tests\Lexer\Lexer\Analyser\Traits;

trait AnnotationProviderTrait
{
    /**
     * @return array<array<mixed>>
     */
    public function annotationProvider(): array
    {
        return [
            [
                <<<'EOD'
                /**
                 * @annotation
                */
                EOD,
                1,
                'annotation',
            ],
            [
                <<<'EOD'
                /**
                 * @annotation()
                */
                EOD,
                1,
                'annotation',
            ],
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function annotationParamLengthProvider()
    {
        return [
            [
                <<<'EOD'
                /**
                 * @annotation(1, 2, 3, 4)
                */
                EOD,
                4,
            ],
            [
                <<<'EOD'
                /**
                 * @annotation()
                */
                EOD,
                0,
            ],
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function annotationObjectParamProvider()
    {
        return [
            [
                <<<'EOD'
                /**
                 * @annotation({
                'key1' = 'test'
                })
                */
                EOD,
                'key1',
            ],
            [
                <<<'EOD'
                /**
                 * @annotation({
                'key2' : 'test'
                })
                */
                EOD,
                'key2',
            ],
            [
                <<<'EOD'
                /**
                 * @annotation({
                'key3':'test'
                })
                */
                EOD,
                'key3',
            ],
            [
                <<<'EOD'
                /**
                 * @annotation({
                'key4'='test'
                })
                */
                EOD,
                'key4',
            ],
        ];
    }

    /**
     * @return array<array<mixed>>
     */
    public function annotationStringProvider()
    {
        return [
            [
                <<<'EOD'
                /**
                 * @annotation('string1')
                */
                EOD,
                <<<'EOD'
                string1
                EOD,
            ],
            [
                <<<'EOD'
                /**
                 * @annotation("string2")
                */
                EOD,
                <<<'EOD'
                string2
                EOD,
            ],
            [
                <<<'EOD'
                /**
                 * @annotation("\"string3")
                */
                EOD,
                <<<'EOD'
                "string3
                EOD,
            ],
            [
                <<<'EOD'
                /**
                 * @annotation("'string4")
                */
                EOD,
                <<<'EOD'
                'string4
                EOD,
            ],
            [
                <<<'EOD'
                /**
                 * @annotation('\'string5')
                */
                EOD,
                <<<'EOD'
                'string5
                EOD,
            ],
            [
                <<<'EOD'
                /**
                 * @annotation('"string6')
                */
                EOD,
                <<<'EOD'
                "string6
                EOD,
            ],
        ];
    }
}
