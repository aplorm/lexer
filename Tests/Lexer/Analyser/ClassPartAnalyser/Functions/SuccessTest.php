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

namespace Aplorm\Lexer\Tests\Lexer\Analyser\ClassPartAnalyser\Functions;

use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Analyser\ClassPartAnalyser;
use Aplorm\Lexer\LexedPartInterface;
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

    public function testFunc(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo() {}
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

        self::assertEquals(LexedPartInterface::FUNCTION_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('public', $partData['visibility']);
    }

    public function testReturnTypeData(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(): ?string {}
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

        self::assertEquals(LexedPartInterface::FUNCTION_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('string', $partData['returnType']['type']);
        self::assertTrue($partData['returnType']['nullable']);
    }

    public function testFunctionContent(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo() {
            if (true) {}
            else {
                while(true) {
                    if(false) {}
                }
            }
        }
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

        self::assertEquals(LexedPartInterface::FUNCTION_PART, $partType);
        self::assertEquals('foo', $partName);
    }

    public function testAbstractFunction(): void
    {
        $code = <<< 'EOT'
        <?php

        abstract public function foo();
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

        self::assertEquals(LexedPartInterface::FUNCTION_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('public', $partData['visibility']);
    }

    public function testAbstractReturnFunction(): void
    {
        $code = <<< 'EOT'
        <?php

        abstract public function foo(): ?string;
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

        self::assertEquals(LexedPartInterface::FUNCTION_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('public', $partData['visibility']);
        self::assertEquals('string', $partData['returnType']['type']);
        self::assertTrue($partData['returnType']['nullable']);
    }

    public function testParameterName(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo($str) {}
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
        self::assertEquals(1, \count($partData['parameters']));
        self::assertArrayHasKey('$str', $partData['parameters']);
    }

    public function testParameterType(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string $str) {}
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
        self::assertEquals(1, \count($partData['parameters']));
        self::assertEquals('string', $partData['parameters']['$str']['type']);
        self::assertFalse($partData['parameters']['$str']['nullable']);
    }

    public function testParameterNullable(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(?string $str) {}
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
        self::assertEquals(1, \count($partData['parameters']));
        self::assertTrue($partData['parameters']['$str']['nullable']);
    }

    public function testParameterDefaultValue(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string $str = 'bla') {}
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
        self::assertEquals(1, \count($partData['parameters']));
        self::assertEquals('bla', $partData['parameters']['$str']['defaultValue']);
    }

    public function testParameterDefaultValueHeredoc(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string $str = <<< 'EOD'
        bla bla
        EOD) {}
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
        self::assertEquals(1, \count($partData['parameters']));
        self::assertEquals('bla bla', $partData['parameters']['$str']['defaultValue']);
    }

    public function testParameterDefaultValueArray(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string $str = [
            ['A' => 'B'],
            ['A','B'],
            'A',
        ]) {}
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
        self::assertEquals(1, \count($partData['parameters']));

        self::assertArrayHasKey('A', $partData['parameters']['$str']['defaultValue'][0]);
        self::assertContains('A', $partData['parameters']['$str']['defaultValue'][1]);
        self::assertContains('A', $partData['parameters']['$str']['defaultValue']);
    }

    public function testParameterDefaultValueOldArray(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string $str = array(
            ['A' => 'B'],
            ['A','B'],
            'A',
        )) {}
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
        self::assertEquals(1, \count($partData['parameters']));

        self::assertArrayHasKey('A', $partData['parameters']['$str']['defaultValue'][0]);
        self::assertContains('A', $partData['parameters']['$str']['defaultValue'][1]);
        self::assertContains('A', $partData['parameters']['$str']['defaultValue']);
    }
}
