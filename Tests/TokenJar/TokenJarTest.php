<?php


namespace Aplorm\Lexer\Tests;


use Aplorm\Common\Test\AbstractTest;
use Aplorm\Lexer\DocBlock\AnnotationData;
use Aplorm\Lexer\Reader\DocBlockReader;
use Aplorm\Lexer\Token\StringTokenJar;
use PhpCsFixer\DocBlock\DocBlock;

class TokenJarTest extends AbstractTest
{
    private ?StringTokenJar $jar = null;

    private int $length = -1;

    private ?string $expectedToken = 'NOT_USED_TOKEN';

    protected function doSetup(): void
    {
    }

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
     * @dataProvider lengthProvider
     * @param string $docBlock
     * @param int $length
     */
    public function testDockBlockLength(string $docBlock, int $length): void
    {
        $jar = new StringTokenJar($docBlock);
        self::assertEquals($length, $jar->count());
    }

    /**
     * @dataProvider emptyProvider
     * @param string $docBlock
     * @param bool $empty
     */
    public function testDockBlockIsEmpty(string $docBlock, bool $empty): void
    {
        $jar = new StringTokenJar($docBlock);
        self::assertEquals($empty, $jar->isEmpty());
    }

    /**
     * @dataProvider tokenProvider
     * @param string $docBlock
     * @param string|null $token
     */
    public function testGetToken(string $docBlock, ?string $token): void
    {
        $jar = new StringTokenJar($docBlock);
        self::assertEquals($token, $jar->get());
    }

    /**
     * @dataProvider nextProvider
     * @param string $docBlock
     * @param mixed $token
     */
    public function testNextToken(string $docBlock, $token): void
    {
        $jar = new StringTokenJar($docBlock);
        self::assertEquals($token, $jar->next());
    }

    /**
     * @dataProvider annotationsProvider
     * @param string $docBlock
     * @param array $annotations
     */
    public function testDocBlockReader(string $docBlock, array $annotations): void
    {
        $jar = new StringTokenJar($docBlock);
        $reader = new DocBlockReader($jar);

        self::assertEquals($annotations, $reader->getAnnotations());
    }


    /**
     * @dataProvider annotationNameProvider
     * @param string $docBlock
     * @param string $annotationName
     * @param mixed $expected
     */
    public function testGetAnnotation(string $docBlock, string $annotationName, $expected): void
    {
        $jar = new StringTokenJar($docBlock);
        $reader = new DocBlockReader($jar);
        if (null === $expected) {
            self::assertNull($reader->getAnnotation($annotationName));
        } else {
            self::assertInstanceOf($expected, $reader->getAnnotation($annotationName));
        }
    }

    /**
     * @dataProvider annotationParameterProvider
     * @param string $docBlock
     * @param string $annotation
     * @param array $exepectedParameter
     */
    public function testGetParameter(string $docBlock, string $annotation, ?array $exepectedParameter): void
    {
        $jar = new StringTokenJar($docBlock);
        $reader = new DocBlockReader($jar);

        self::assertEquals($exepectedParameter, $reader->getAnnotation($annotation)->getParameters());
    }

    public function lengthProvider()
    {
        yield [
            '',
            0,
        ];

        yield [
          <<< EOT
/**
*/
EOT,
        5
        ];
    }

    public function emptyProvider()
    {
        yield [
            '',
            true,
        ];

        yield [
          <<< EOT
/**
*/
EOT,
        false
        ];
    }

    public function tokenProvider()
    {
        yield [
            '',
            null,
        ];

        yield [
            <<< EOT
/**
*/
EOT,
            '/'
        ];
    }

    public function nextProvider()
    {
        yield [
            '',
            null,
        ];
        yield [
            <<< EOT
/**
*/
EOT,
            '*'
        ];
    }

    public function annotationsProvider()
    {
        yield [
            '',
            []
        ];
        yield [
            <<< EOT
/**
* @annotation
*/
EOT,
            [
                'annotation' => new AnnotationData('annotation')
            ]
        ];
        yield [
            <<< EOT
/**
* @
*/
EOT,
            []
        ];
        yield [
            <<< EOT
/**
* @annotation()
*/
EOT,
            [
                'annotation' => new AnnotationData('annotation')
            ]
        ];
    }

    public function annotationNameProvider()
    {
        yield [
            <<< EOT
/**
* @annotation
*/
EOT,
            'annotation',
            AnnotationData::class,
        ];
        yield [
            <<< EOT
/**
* @annotation
*/
EOT,
            'Foo',
            null,
        ];
    }

    public function annotationParameterProvider()
    {
        yield [
            <<< EOT
/**
* @annotation
*/
EOT,
            'annotation',
            null
        ];

        yield [
            <<< EOT
/**
* @annotation()
*/
EOT,
            'annotation',
            null
        ];

        yield [
            <<< EOT
/**
* @annotation(1)
*/
EOT,
            'annotation',
            [
                1
            ]
        ];
    }
}
