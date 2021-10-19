<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZETest\ContentValidation\Extractor;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Stream;
use Laminas\Diactoros\UploadedFile;
use Laminas\Http\Request as LaminasRequest;
use Laminas\Router\Http\TreeRouteStack;
use Mezzio\Router\LaminasRouter;
use Mezzio\Router\Route;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Server\MiddlewareInterface;
use ZE\ContentValidation\Extractor\BodyExtractor;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\DataExtractorInterface;
use ZE\ContentValidation\Extractor\FileExtractor;
use ZE\ContentValidation\Extractor\ParamsExtractor;
use ZE\ContentValidation\Extractor\QueryExtractor;

class DataExtractorChainTest extends TestCase
{
    use ProphecyTrait;

    public function testGetDataFromRequestFromEmptyChain(): void
    {
        $dataExtractorChain = new DataExtractorChain([]);
        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([]);
        $actual = $dataExtractorChain->getDataFromRequest($request);

        self::assertCount(0, $actual);
    }

    private function getMiddleware(): MiddlewareInterface
    {
        return $this->prophesize(MiddlewareInterface::class)->reveal();
    }

    public function testGetDataFromRequestDefaultExtraction(): void
    {
        $extractors = [
            $firstExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock(),
            $secondExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock(),
        ];

        $dataExtractorChain = new DataExtractorChain($extractors);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([
                'Foo' => 'FooBar',
                'Fizz' => 'Buzz',
            ]);

        $firstExtractor->expects(self::any())->method('extractData')->will(
            self::returnValue([
                'Foo' => 'Bar',
                'Bar' => 'Foo',
            ])
        );

        $secondExtractor->expects(self::any())->method('extractData')->will(
            self::returnValue([
                'Foo' => 'FooBar',
                'Fizz' => 'Buzz',
            ])
        );

        $actual = $dataExtractorChain->getDataFromRequest($request);

        self::assertSame(
            [
                'Foo' => 'FooBar',
                'Bar' => 'Foo',
                'Fizz' => 'Buzz',
            ],
            $actual
        );
    }

    public function testGetDataFromRequesExtractTraversable(): void
    {
        $extractors = [
            $firstExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock(),
            $secondExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock(),
        ];

        $dataExtractorChain = new DataExtractorChain($extractors);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([
                'Foo' => 'FooBar',
                'Fizz' => 'Buzz',
            ]);

        $firstExtractor->expects(self::any())->method('extractData')->will(
            self::returnValue(
                new \ArrayIterator([
                    'Foo' => [
                        'Fizz' => 'Buzz',
                    ],
                ])
            )
        );

        $secondExtractor->expects(self::any())->method('extractData')->will(
            self::returnValue(
                new \ArrayIterator([
                    'Foo' => [
                        'Fizz' => 'Bar',
                    ],
                ])
            )
        );

        $actual = $dataExtractorChain->getDataFromRequest($request);

        self::assertSame(
            [
                'Foo' => [
                    'Fizz' => 'Bar',
                ],
            ],
            $actual
        );
    }

    /**
     * //@expectedException ZE\ContentValidation\Exception\UnexpectedValueException
     */
    public function testGetDataFromRequestInvalidExtraction(): void
    {

        $extractors = [
            $extractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock(),
        ];

        $dataExtractorChain = new DataExtractorChain($extractors);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([
                'Foo' => 'FooBar',
                'Fizz' => 'Buzz',
            ]);

        $extractor->expects(self::any())->method('extractData')->will(self::returnValue(new \stdClass()));

        $dataExtractorChain->getDataFromRequest($request);
    }

    public function testParamsExtractorExtractDataFromRequestOnPostAndIsOk(): void
    {
        $routeParams = ['id' => 1];
        $route = $this->prophesize(Route::class);
        $route->getName()->willReturn('contacts');

        $routeMatch = new \Laminas\Router\Http\RouteMatch($routeParams, 1);
        $routeMatch->setMatchedRouteName('contacts');
        $laminasRouter = $this->prophesize(TreeRouteStack::class);
        $laminasRouter->match(Argument::type(LaminasRequest::class))->willReturn($routeMatch);
        $middleware = $this->getMiddleware();
        $laminasRouter->addRoute('contacts', [
            'type' => 'segment',
            'options' => [
                'route' => '/contacts[/:id]',
            ],
            'may_terminate' => false,
            'child_routes' => [
                "GET:DELETE:PATCH:PUT:POST" => [
                    'type' => 'method',
                    'options' => [
                        'verb' => "GET,DELETE,PATCH,PUT,POST",
                        'defaults' => [
                            'middleware' => $middleware,
                        ],
                    ],
                ],
                "method_not_allowed" => [
                    "type" => "regex",
                    "priority" => -1,
                    "options" => [
                        "regex" => "",
                        "defaults" =>
                            [
                                "method_not_allowed" => "/contacts[/:id]",
                            ],
                        "spec" => "",
                    ],
                ],
            ],
        ])->shouldBeCalled();
        $router = new LaminasRouter($laminasRouter->reveal());
        $router->addRoute(
            new Route(
                '/contacts[/:id]',
                $middleware,
                [
                    RequestMethod::METHOD_GET,
                    RequestMethod::METHOD_DELETE,
                    RequestMethod::METHOD_PATCH,
                    RequestMethod::METHOD_PUT,
                    RequestMethod::METHOD_POST,
                ],
                'contacts'
            )
        );
        $extractor = new ParamsExtractor($router);

        $data = [
            'id' => 1,
        ];

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('PUT')
            ->withParsedBody($data);

        $actual = $extractor->extractData($request);

        self::assertSame(
            $routeParams,
            $actual
        );
    }

    public function testBodyExtractorExtractDataFromRequestOnPostAndIsOk(): void
    {
        $extractor = new BodyExtractor();
        $data = [
            'Foo' => 'FooBar',
            'Fizz' => 'Buzz',
        ];

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody($data);

        $actual = $extractor->extractData($request);

        self::assertSame(
            $data,
            $actual
        );
    }

    public function testQueryExtractorExtractDataFromRequestOnGetAndIsOk(): void
    {
        $extractor = new QueryExtractor();
        $data = [
            'Foo' => 'FooBar',
            'Fizz' => 'Buzz',
        ];

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('GET')
            ->withQueryParams($data);

        $actual = $extractor->extractData($request);

        self::assertSame(
            $data,
            $actual
        );
    }

    public function testFileExtractorExtractDataFromRequestOnPostAndIsOk(): void
    {
        $extractor = new FileExtractor();
        $data = [
            'filename' => [
                'tmp_name' => null,
                'name' => '/tmp/12345678adf',
                'type' => 'text/plain',
                'size' => 10,
                'error' => UPLOAD_ERR_OK,
            ],
        ];

        $uploadedFile = $this->prophesize(UploadedFile::class);

        $uploadedFile->getStream()->willReturn($this->prophesize(Stream::class)->reveal());
        $uploadedFile->getClientFilename()->willReturn('/tmp/12345678adf');
        $uploadedFile->getClientMediaType()->willReturn('text/plain');
        $uploadedFile->getSize()->willReturn(10);
        $uploadedFile->getError()->willReturn(UPLOAD_ERR_OK);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withUploadedFiles([
                'filename' => $uploadedFile->reveal(),
            ]);

        $actual = $extractor->extractData($request);

        self::assertSame(
            $data,
            $actual
        );
    }
}
