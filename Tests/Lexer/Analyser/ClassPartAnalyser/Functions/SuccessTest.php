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

use Aplorm\Common\Lexer\LexedPartInterface;
use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Analyser\ClassPartAnalyser;
use Aplorm\Lexer\Analyser\DocBlockAnalyser;
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

        self::assertEquals(LexedPartInterface::METHOD_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('public', $partData['visibility']);
        self::assertEquals([], $partData['annotations']);
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

        self::assertEquals(LexedPartInterface::METHOD_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('string', $partData['returnType']['type']);
        self::assertTrue($partData['returnType']['nullable']);
        self::assertEquals([], $partData['annotations']);
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

        self::assertEquals(LexedPartInterface::METHOD_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals([], $partData['annotations']);
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

        self::assertEquals(LexedPartInterface::METHOD_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('public', $partData['visibility']);
        self::assertEquals([], $partData['annotations']);
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

        self::assertEquals(LexedPartInterface::METHOD_PART, $partType);
        self::assertEquals('foo', $partName);
        self::assertEquals('public', $partData['visibility']);
        self::assertEquals('string', $partData['returnType']['type']);
        self::assertTrue($partData['returnType']['nullable']);
        self::assertEquals([], $partData['annotations']);
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
        self::assertArrayHasKey('str', $partData['parameters']);
        self::assertEquals([], $partData['annotations']);
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
        self::assertEquals('string', $partData['parameters']['str']['type']);
        self::assertFalse($partData['parameters']['str']['nullable']);
        self::assertEquals([], $partData['annotations']);
    }

    public function testFunctionAnnotations(): void
    {
        $annotationContent = <<< 'EOT'
        /**
         * @param string|null $str
         * @annotation
         */
        EOT;

        $annotations = DocBlockAnalyser::analyse($annotationContent);

        $code = <<< 'EOT'
        <?php

        public function foo(string $str) {}
        EOT;

        $tokens = token_get_all($code);
        $iterator = 1;
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertArrayHasKey('annotations', $partData);
        self::assertEquals(1, \count($partData['annotations']));
    }

    public function testFunctionExcluedAnnotations(): void
    {
        $annotationContent = <<< 'EOT'
        /**
         * @author string|null $str
         * @see
         */
        EOT;

        $annotations = DocBlockAnalyser::analyse($annotationContent);

        $code = <<< 'EOT'
        <?php

        public function foo(string $str) {}
        EOT;

        $tokens = token_get_all($code);
        $iterator = 1;
        ClassPartAnalyser::init($tokens, $iterator, \count($tokens));
        [
            'partType' => $partType,
            'partName' => $partName,
            'partData' => $partData,
        ] = ClassPartAnalyser::analyse($annotations);

        self::assertEquals([], $partData['annotations']);
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
        self::assertTrue($partData['parameters']['str']['nullable']);
        self::assertEquals([], $partData['annotations']);
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
        self::assertEquals('bla', $partData['parameters']['str']['value']);
        self::assertEquals([], $partData['annotations']);
    }

    public function testParameterIsNotConstantDefaultValue(): void
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
        self::assertEquals('bla', $partData['parameters']['str']['value']);
        self::assertFalse($partData['parameters']['str']['isValueAConstant']);
        self::assertEquals([], $partData['annotations']);
    }

    public function testParameterIsConstantDefaultValue(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string $str = CONSTANT) {}
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
        self::assertTrue($partData['parameters']['str']['isValueAConstant']);
        self::assertEquals([], $partData['annotations']);
    }

    public function testParameterIsMultipleConstantDefaultValue(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string $str = CONSTANT, $param = true) {}
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

        self::assertEquals(2, \count($partData['parameters']));
        self::assertTrue($partData['parameters']['str']['isValueAConstant']);
        self::assertEquals('CONSTANT', $partData['parameters']['str']['value']);
        self::assertTrue($partData['parameters']['param']['isValueAConstant']);
        self::assertEquals('true', $partData['parameters']['param']['value']);
        self::assertEquals([], $partData['annotations']);
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
        self::assertEquals('bla bla', $partData['parameters']['str']['value']);
        self::assertEquals([], $partData['annotations']);
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

        self::assertArrayHasKey('A', $partData['parameters']['str']['value'][0]);
        self::assertContains('A', $partData['parameters']['str']['value'][1]);
        self::assertContains('A', $partData['parameters']['str']['value']);
        self::assertEquals([], $partData['annotations']);
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

        self::assertArrayHasKey('A', $partData['parameters']['str']['value'][0]);
        self::assertContains('A', $partData['parameters']['str']['value'][1]);
        self::assertContains('A', $partData['parameters']['str']['value']);
        self::assertEquals([], $partData['annotations']);
    }

    public function testReferenceParameter(): void
    {
        $code = <<< 'EOT'
        <?php

        public function foo(string &$str) {}
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
        self::assertArrayHasKey('str', $partData['parameters']);
        self::assertEquals([], $partData['annotations']);
    }
}
