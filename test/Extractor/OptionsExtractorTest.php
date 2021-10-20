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
use Laminas\Http\Request as LaminasRequest;
use Laminas\InputFilter\InputFilter;
use Laminas\Router\Http\TreeRouteStack;
use Mezzio\Router\LaminasRouter;
use Mezzio\Router\Route;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use ZE\ContentValidation\Extractor\OptionsExtractor;

class OptionsExtractorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var array<string, array<string, class-string>>
     */
    private array $configValidation;
    private RouterInterface $router;
    private static string $url = 'https://github.com';

    protected function setUp(): void
    {
        $this->configValidation = [];

        $middleware = $this->getMiddleware();
        $route = $this->prophesize(Route::class);
        $route->getName()->willReturn('contacts');
        $routeMatch = new \Laminas\Router\Http\RouteMatch([1], 1);
        $routeMatch->setMatchedRouteName('contacts');
        $laminasRouter = $this->prophesize(TreeRouteStack::class);
        $laminasRouter->match(Argument::type(LaminasRequest::class))->willReturn($routeMatch);
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

        $this->router = $router;
    }

    private function getMiddleware(): MiddlewareInterface
    {
        return $this->prophesize(MiddlewareInterface::class)->reveal();
    }

    public function testNoOptionsWithRouteMatchReturnsEmptyValidationConfig(): void
    {
        /**
         * Test no options with route match
         */
        $optionExtractor = new OptionsExtractor($this->configValidation, $this->router);
        self::assertEquals(
            [],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestProphecy(self::$url)->reveal()
            )
        );
    }

    public function testOptionsExistWithRouteMatchReturnsARightValidatorConfig(): void
    {

        /**
         * Test options exist with route match
         */
        $this->applyValidationConfig();
        $optionExtractor = new OptionsExtractor($this->configValidation, $this->router);

        self::assertEquals(
            $this->configValidation['contacts'],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestProphecy(self::$url)->reveal()
            )
        );
        /**
         * Test options exist no route match
         */
        self::assertEquals(
            $this->configValidation['contacts'],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestProphecy('')->reveal()
            )
        );
    }

    /**
     * @return ObjectProphecy<ServerRequestInterface>
     */
    public function getRequestProphecy(
        string $uriString,
        string $requestMethod = RequestMethod::METHOD_GET
    ): ObjectProphecy {
        $request = $this->prophesize(ServerRequestInterface::class);
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()->willReturn($uriString);
        $uri->__toString()->willReturn('http://www.example.com/' . $uriString);
        $request->getMethod()->willReturn($requestMethod);
        $request->getUri()->will([$uri, 'reveal']);
        $request->getHeaders()->willReturn([]);
        $request->getCookieParams()->willReturn([]);
        $request->getQueryParams()->willReturn([]);
        $request->getServerParams()->willReturn([]);

        return $request;
    }

    private function applyValidationConfig(): void
    {
        $this->configValidation = [
            // route name
            'contacts' => [
                '*' => InputFilter::class,
            ],
        ];
    }
}
