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
use Aplorm\Lexer\Tests\Lexer\Analyser\Traits\FileDataProviderTrait;

class SuccessTest extends AbstractTest
{
    use FileDataProviderTrait;

    /**
     * function call in setUp function.
     */
    protected function doSetup(): void
    {
    }

    /**
     * function call in tearDown function.
     */
    protected function doTearDown(): void
    {
    }

    public static function setupBeforeClass(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
    }

    public function testVariable(): void
    {
        $code = <<< 'EOT'
        <?php

        private string $str = A_CONSTANT;
        EOT;
        $tokens = token_get_all($code);
        $iterator = 1;
        $annotations = [];
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals(LexedPartInterface::VARIABLE_PART, $partType);
        self::assertEquals('$str', $partName);
        self::assertEquals('private', $partData['visibility']);
        self::assertTrue($partData['isValueAConstant']);
    }

    public function testIntegerDefaultValue(): void
    {
        $code = <<< 'EOT'
        <?php

        private string $str = 1;
        EOT;
        $tokens = token_get_all($code);
        $iterator = 1;
        $annotations = [];
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals(LexedPartInterface::VARIABLE_PART, $partType);
        self::assertEquals('$str', $partName);
        self::assertEquals('private', $partData['visibility']);
        self::assertFalse($partData['isValueAConstant']);
    }

    public function testNullableVariable(): void
    {
        $code = <<< 'EOT'
        <?php

        private ?string $str;
        EOT;
        $tokens = token_get_all($code);
        $iterator = 1;
        $annotations = [];
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals(LexedPartInterface::VARIABLE_PART, $partType);
        self::assertEquals('$str', $partName);
        self::assertEquals('private', $partData['visibility']);
        self::assertTrue($partData['nullable']);
        self::assertArrayNotHasKey('isValueAConstant', $partData);
    }

    public function testDefaultValue(): void
    {
        $code = <<< 'EOT'
        <?php

        private string $str = 'bla bla';
        EOT;
        $tokens = token_get_all($code);
        $iterator = 1;
        $annotations = [];
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals('bla bla', $partData['value']);
        self::assertFalse($partData['isValueAConstant']);
    }

    public function testArrayValue(): void
    {
        $code = <<< 'EOT'
        <?php

        private array $str = [
            ['A' => 'B'],
            ['A','B'],
            'A',
        ];
        EOT;
        $tokens = token_get_all($code);
        $iterator = 1;
        $annotations = [];
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals(3, \count($partData['value']));
        self::assertArrayHasKey('A', $partData['value'][0]);
        self::assertContains('A', $partData['value'][1]);
        self::assertContains('A', $partData['value']);
    }

    public function testOldArraySyntaxValue(): void
    {
        $code = <<< 'EOT'
        <?php

        private array $str = array(
            ['A' => 'B'],
            ['A','B'],
            'A',
        );
        EOT;
        $tokens = token_get_all($code);
        $iterator = 1;
        $annotations = [];
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals(3, \count($partData['value']));
        self::assertArrayHasKey('A', $partData['value'][0]);
        self::assertContains('A', $partData['value'][1]);
        self::assertContains('A', $partData['value']);
    }

    public function testHeredoc(): void
    {
        $code = <<< 'EOT'
        <?php

        private array $str = <<<'EOD'
           bla bla bla
        EOD;
        EOT;
        $tokens = token_get_all($code);
        $iterator = 1;
        $annotations = [];
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals('bla bla bla', $partData['value']);
    }
}
