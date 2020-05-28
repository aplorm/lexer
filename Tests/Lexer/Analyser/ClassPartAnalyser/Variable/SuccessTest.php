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

namespace Aplorm\Lexer\Tests\Lexer\Analyser\ClassPartAnalyser\Variable;

use Aplorm\Common\Lexer\LexedPartInterface;
use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Analyser\ClassPartAnalyser;
use Aplorm\Lexer\Analyser\DocBlockAnalyser;
use Aplorm\Lexer\Tests\Lexer\Analyser\Traits\FileDataProviderTrait;
use Aplorm\Lexer\Tests\Traits\RecurciveArrayTestTrait;

class SuccessTest extends AbstractTest
{
    use FileDataProviderTrait;
    use RecurciveArrayTestTrait;

    /**
     * @var mixed[]
     */
    private static ?array $annotations = [];

    /**
     * @var mixed[]
     */
    private static ?array $tokens = [];

    /**
     * @var mixed[]
     */
    private static ?array $testedValue = [];

    private static int $tokenLength = 0;

    private static int $iterator = 0;

    /**
     * function call in setUp function.
     */
    protected function doSetup(): void
    {
        [
            $phpCode,
            $annotationCode,
            self::$testedValue,
        ] = $this->getProvidedData();
        self::$iterator = 1;
        if (null !== $annotationCode) {
            self::$annotations = DocBlockAnalyser::analyse($annotationCode);
        }

        self::$tokens = token_get_all($phpCode);
        self::$tokenLength = \count(self::$tokens);
        ClassPartAnalyser::init(self::$tokens, self::$iterator, self::$tokenLength);
    }

    /**
     * function call in tearDown function.
     */
    protected function doTearDown(): void
    {
        ClassPartAnalyser::clean();
        self::$annotations = [];
        self::$tokens = [];
        self::$testedValue = [];
        self::$iterator = 0;
        self::$tokenLength = 0;
    }

    public static function setupBeforeClass(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
    }

    /**
     * @dataProvider fullProvider
     */
    public function testFull(): void
    {
        $expectedValue = ClassPartAnalyser::analyse(self::$annotations);
        $this->recurciveCheck(self::$testedValue, $expectedValue);
    }

    public function fullProvider(): array
    {
        return [
            [
                <<< 'EOT'
    <?php
    
    private string $str = A_CONSTANT;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => true,
                        'value' => 'A_CONSTANT',
                    ],
                ],
            ],
            [
                <<< 'EOT'
    <?php
    
    string $str = A_CONSTANT;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'public',
                        'nullable' => false,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => true,
                        'value' => 'A_CONSTANT',
                    ],
                ],
            ],
            [
                <<< 'EOT'
    <?php
    
    protected string $str = A_CONSTANT;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'protected',
                        'nullable' => false,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => true,
                        'value' => 'A_CONSTANT',
                    ],
                ],
            ],
            [
                <<< 'EOT'
    <?php
    
    protected string $str = A_CONSTANT;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'protected',
                        'nullable' => false,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => true,
                        'value' => 'A_CONSTANT',
                    ],
                ],
            ],
            [
                <<< 'EOT'
    <?php

    protected PDO $pdo;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'pdo',
                    'partData' => [
                        'name' => 'pdo',
                        'visibility' => 'protected',
                        'nullable' => false,
                        'type' => 'PDO',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => null,
                    ],
                ],
            ],
            [
                <<< 'EOT'
    <?php

    private string $str = 1;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => '1',
                    ],
                ],
            ],
            [
                <<< 'EOT'
    <?php

    private ?string $str;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => true,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => null,
                    ],
                ],
            ],
            [
                <<< 'EOT'
        <?php

        private string $str = 'bla bla';
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => 'bla bla',
                    ],
                ],
            ],
            [
                <<< 'EOT'
        <?php

        private string $str = self::A_CONSTANT;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'string',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => true,
                        'value' => 'self::A_CONSTANT',
                    ],
                ],
            ],
            [
                <<< 'EOT'
        <?php

        private array $str = [
            ['A' => 'B'],
            ['A','B'],
            'A',
        ];
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'array',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => [
                            ['A' => 'B'],
                            ['A', 'B'],
                            'A',
                        ],
                    ],
                ],
            ],
            [
                <<< 'EOT'
        <?php

        private array $str = array(
            ['A' => 'B'],
            ['A','B'],
            'A',
        );
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'array',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => [
                            ['A' => 'B'],
                            ['A', 'B'],
                            'A',
                        ],
                    ],
                ],
            ],
            [
                <<< 'EOT'
        <?php

        private array $str = <<<'EOD'
           bla bla bla
        EOD;
    EOT,
                null,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'array',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => 'bla bla bla',
                    ],
                ],
            ],
            [
                <<< 'EOT'
        <?php

        private array $str = <<<'EOD'
           bla bla bla
        EOD;
        EOT,
                <<< 'EOT'
        /**
         * @author string|null $str
         * @see
         */
        EOT,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'array',
                        'static' => false,
                        'annotations' => [],
                        'isValueAConstant' => false,
                        'value' => 'bla bla bla',
                    ],
                ],
            ],
            [
                <<< 'EOT'
        <?php

        private array $str = <<<'EOD'
           bla bla bla
        EOD;
        EOT,
                <<< 'EOT'
        /**
         * @param string|null $str
         * @annotation
         */
        EOT,
                [
                    'partType' => LexedPartInterface::VARIABLE_PART,
                    'partName' => 'str',
                    'partData' => [
                        'name' => 'str',
                        'visibility' => 'private',
                        'nullable' => false,
                        'type' => 'array',
                        'static' => false,
                        'annotations' => [
                            [
                                'name' => 'annotation',
                                'params' => [],
                            ],
                        ],
                        'isValueAConstant' => false,
                        'value' => 'bla bla bla',
                    ],
                ],
            ],
        ];
    }
}
