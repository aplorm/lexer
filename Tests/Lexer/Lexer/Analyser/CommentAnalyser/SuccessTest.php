<?php

declare(strict_types=1);

namespace Orm\Lexer\Tests\Lexer\Lexer\Analyser\CommentAnalyser;

use Orm\Common\Test\AbstractTest;
use Orm\Lexer\Analyser\DocBlockAnalyser;
use Orm\Lexer\Tests\Lexer\Lexer\Analyser\Traits\AnnotationProviderTrait;

class SuccessTest extends AbstractTest
{
    use AnnotationProviderTrait;

    /**
     * function call in setUp function.
     */
    protected function doSetup()
    {
    }

    /**
     * function call in tearDown function.
     */
    protected function doTearDown()
    {
    }

    public static function setupBeforeClass(): void
    {
    }

    public static function tearDownAfterClass(): void
    {
    }

    /**
     * @dataProvider annotationProvider
     */
    public function testAnnotation($docBloc, $count, $name)
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);

        self::assertIsArray($annotations);
        self::assertEquals($count, \count($annotations));
        self::assertEquals($name, $annotations[0]['name']);
    }

    /**
     * @dataProvider annotationParamLengthProvider
     */
    public function testParamsLength($docBloc, $length)
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertEquals($length, \count($annotations[0]['params']));
    }

    /**
     * @dataProvider annotationObjectParamProvider
     */
    public function testObjectKey($docBloc, $key)
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertArrayHasKey($key, $annotations[0]['params'][0]['value']);
    }

    public function testNamedParameter()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(namedParameter = 'test')
 */
EOD;
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertEquals(1, \count($annotations));
        self::assertEquals('namedParameter', $annotations[0]['params'][0]['name']);
    }

    /**
     * @dataProvider annotationStringProvider
     */
    public function testStringParameter($docBloc, $value)
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertEquals($value, $annotations[0]['params'][0]['value']);
    }
}
