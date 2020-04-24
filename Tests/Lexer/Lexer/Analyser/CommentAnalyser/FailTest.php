<?php

declare(strict_types=1);

namespace Orm\Lexer\Tests\Lexer\Lexer\Analyser\CommentAnalyser;

use Orm\Common\Test\AbstractTest;
use Orm\Lexer\Analyser\DocBlockAnalyser;
use Orm\Lexer\Exception\AnnotationSyntaxException;

class FailTest extends AbstractTest
{
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

    public function testWrongDocBlock()
    {
        $docBloc = <<<'EOD'
/*** @annotation*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testMissignParenthesis()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(1*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongParamFormat()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(
*
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongStringFormat()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation("1
*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongEndString()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation("1""
*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongObjectFormat()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation({1, 2)
*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongObjectEndFormat()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation({1, 2*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongDoublComaInObject()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation({1,,2})
 */
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testNoNameNamedParameter()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(= 'test')
 */
EOD;

        $this->expectException(AnnotationSyntaxException::class);
        $annotations = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongNameNamedParameter()
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(class::constant = 'test')
 */
EOD;

        $this->expectException(AnnotationSyntaxException::class);
        $annotations = DocBlockAnalyser::analyse($docBloc);
    }
}
