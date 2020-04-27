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

namespace Aplorm\Lexer\Tests\Lexer\Analyser\CommentAnalyser;

use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\Analyser\DocBlockAnalyser;
use Aplorm\Lexer\Tests\Lexer\Analyser\Traits\AnnotationProviderTrait;

class SuccessTest extends AbstractTest
{
    use AnnotationProviderTrait;

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

    /**
     * @dataProvider annotationProvider
     */
    public function testAnnotation(string $docBloc, int $count, string $name): void
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);

        self::assertIsArray($annotations);
        self::assertEquals($count, \count($annotations));
        self::assertEquals($name, $annotations[0]['name']);
    }

    /**
     * @dataProvider annotationParamLengthProvider
     */
    public function testParamsLength(string $docBloc, int $length): void
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertEquals($length, \count($annotations[0]['params']));
    }

    /**
     * @dataProvider annotationObjectParamProvider
     */
    public function testObjectKey(string $docBloc, string $key): void
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertArrayHasKey($key, $annotations[0]['params'][0]['value']);
    }

    public function testNamedParameter(): void
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

    public function testExcluedAnnotation(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @param
 */
EOD;
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertEquals(0, \count($annotations));
    }

    /**
     * @dataProvider annotationStringProvider
     */
    public function testStringParameter(string $docBloc, string $value): void
    {
        $annotations = DocBlockAnalyser::analyse($docBloc);
        self::assertEquals($value, $annotations[0]['params'][0]['value']);
    }
}
