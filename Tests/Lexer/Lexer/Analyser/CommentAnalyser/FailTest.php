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

namespace Orm\Lexer\Tests\Lexer\Lexer\Analyser\CommentAnalyser;

use Orm\Common\Test\AbstractTest;
use Orm\Lexer\Analyser\DocBlockAnalyser;
use Orm\Lexer\Exception\AnnotationSyntaxException;

class FailTest extends AbstractTest
{
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

    public function testWrongDocBlock(): void
    {
        $docBloc = <<<'EOD'
/*** @annotation*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testMissignParenthesis(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(1*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongParamFormat(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(
*
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongStringFormat(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation("1
*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongEndString(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation("1""
*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongObjectFormat(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation({1, 2)
*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongObjectEndFormat(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation({1, 2*/
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongDoublComaInObject(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation({1,,2})
 */
EOD;

        $this->expectException(AnnotationSyntaxException::class);

        $lexer = DocBlockAnalyser::analyse($docBloc);
    }

    public function testNoNameNamedParameter(): void
    {
        $docBloc = <<<'EOD'
/**
 *  @annotation(= 'test')
 */
EOD;

        $this->expectException(AnnotationSyntaxException::class);
        $annotations = DocBlockAnalyser::analyse($docBloc);
    }

    public function testWrongNameNamedParameter(): void
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
