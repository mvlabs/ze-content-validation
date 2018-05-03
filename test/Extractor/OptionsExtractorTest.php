<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */

declare(strict_types=1);

namespace ZETest\ContentValidation\Validator;

use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\ZendRouter;
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Zend\Router\Http\TreeRouteStack;
use Zend\Stratigility\Http\Request;
use Zend\Http\Request as ZendRequest;

class OptionExtractorTest extends PHPUnit_Framework_TestCase
{
    private $config;
    private $configValidation;
    /**
     * @var RouterInterface $router
     */
    private $router;
    private static $url = 'http://mvlabs.it';

    protected function setUp()
    {
        $this->configValidation = [];

        $middleware = $this->getMiddleware();
        $route = $this->prophesize(Route::class);
        $route->getName()->willReturn('contacts');
        $routeMatch = new \Zend\Router\Http\RouteMatch([1], 1);
        $routeMatch->setMatchedRouteName('contacts');
        $this->zendRouter = $this->prophesize(TreeRouteStack::class);
        $this->zendRouter->match(Argument::type(ZendRequest::class))->willReturn($routeMatch);
        $this->zendRouter->addRoute('contacts', [
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
                                "method_not_allowed" => "/contacts[/:id]"
                            ],
                        "spec" => ""
                    ]
                ]
            ],
        ])->shouldBeCalled();

        $router = new ZendRouter($this->zendRouter->reveal());
        $router->addRoute(new Route(
            '/contacts[/:id]',
            $middleware,
            [
                RequestMethod::METHOD_GET,
                RequestMethod::METHOD_DELETE,
                RequestMethod::METHOD_PATCH,
                RequestMethod::METHOD_PUT,
                RequestMethod::METHOD_POST
            ],
            'contacts'
        ));

        $this->router = $router;
    }

    private function getMiddleware(): MiddlewareInterface
    {
        return $this->prophesize(MiddlewareInterface::class)->reveal();
    }

    public function testNoOptionsWithRouteMatchReturnsEmptyValidationConfig()
    {
        /**
         * Test no options with route match
         */
        $optionExtractor = new OptionsExtractor($this->configValidation, $this->router);
        $this->assertEquals(
            [],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestProphecy(self::$url)->reveal()
            )
        );
    }


    public function testOptionsExistWithRouteMatchReturnsARightValidatorConfig()
    {

        /**
         * Test options exist with route match
         */
        $this->applyValidationConfig();
        $optionExtractor = new OptionsExtractor($this->configValidation, $this->router);

        $this->assertEquals(
            $this->configValidation['contacts'],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestProphecy(self::$url)->reveal()
            )
        );
        /**
         * Test options exist no route match
         */
        $this->assertEquals(
            $this->configValidation['contacts'],
            $optionExtractor->getOptionsForRequest(

                $this->getRequestProphecy('')->reveal()
            )
        );
    }

    public function getRequestProphecy($uriString, $requestMethod = RequestMethod::METHOD_GET)
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()->willReturn($uriString);
        $uri->__toString()->willReturn('http://www.example.com/'.$uriString);
        $request->getMethod()->willReturn($requestMethod);
        $request->getUri()->will([$uri, 'reveal']);
        $request->getHeaders()->willReturn([]);
        $request->getCookieParams()->willReturn([]);
        $request->getQueryParams()->willReturn([]);
        $request->getServerParams()->willReturn([]);

        return $request;
    }

    /**
     * Helper for applying the validation
     */
    private function applyValidationConfig()
    {
        $this->configValidation = [
            'contacts' => [ //route name
                '*' => ContactInputFilter::class
            ],
        ];
    }
}
