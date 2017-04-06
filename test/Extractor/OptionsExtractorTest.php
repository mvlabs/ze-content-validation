<?php
namespace ZETest\ContentValidation\Validator;

use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_TestCase;
use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\ZendRouter;
use Zend\Stratigility\Http\Request;

class OptionExtractorTest extends TestCase
{
    private $config;
    /**
     * @var RouterInterface $router
     */
    private $router;
    private static $url = 'http://mvlabs.it';

    protected function setUp()
    {
        $this->config = [
            [
                'name' => 'contacts',
                'path' => '/contacts[/:id]',
                'middleware' => function () {
                },
                'allowed_methods' => ['GET', 'DELETE', 'PATCH', 'PUT', 'POST'],
                'options' => []
            ]
        ];
        $params = $this->config[0]['options'];
        $route = $this->prophesize(Route::class);
        $route->getName()->willReturn('contacts');
        $routeResult = RouteResult::fromRoute($route->reveal(), $params);

        $router = $this->getMockBuilder(ZendRouter::class)->getMock();
        $router->expects($this->any())
            ->method('match')
            ->willReturn($routeResult);
        $this->router = $router;
    }

    public function testNoOptionsWithRouteMatchReturnsEmptyValidationConfig()
    {
        /**
         * Test no options with route match
         */
        $optionExtractor = new OptionsExtractor($this->config, $this->router);
        $this->assertEquals(
            [],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestMock(self::$url)
            )
        );
    }

    public function testOptionsExistWithRouteMatchReturnsARightValidatorConfig()
    {

        /**
         * Test options exist with route match
         */
        $this->applyValidationConfig();
        $optionExtractor = new OptionsExtractor($this->config, $this->router);

        $this->assertEquals(
            $this->config[0]['options'],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestMock(self::$url)
            )
        );
        /**
         * Test options exist no route match
         */

        $this->assertEquals(
            $this->config[0]['options'],
            $optionExtractor->getOptionsForRequest(
                $this->getRequestMock('')
            )
        );
    }

    /**
     * @dataProvider sanitizeProvider
     */
    public function testAllSanitizedWithRouteMatchReturnsARightConfig($key, $value)
    {
        $this->applyValidationConfig();
        $optionExtractor = new OptionsExtractor($this->config, $this->router);
        $result = $optionExtractor->getAllSanitize();
        $this->assertEquals($result[0][$key], $value);
        $this->assertCount(3, $result[0]);
    }

    public function sanitizeProvider()
    {
        return [
            [
                'name', 'contacts'
            ],
            [
                'path', '/contacts[/:id]'
            ],
            [
                'allowed_methods', ['GET', 'DELETE', 'PATCH', 'PUT', 'POST']
            ]
        ];
    }

    /**
     * @param $uriString
     * @param string $method
     * @return Request
     */
    private function getRequestMock($uriString, $method = 'GET')
    {
        $requestMock = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $uri = new Uri($uriString);
        $requestMock->expects($this->any())
            ->method('getUri')
            ->willReturnCallback(function () use ($uri) {
                return $uri;
            });
        $requestMock->expects($this->any())
            ->method('getMethod')
            ->willReturn($method);
        return $requestMock;
    }

    /**
     * Helper for applying the validation
     */
    private function applyValidationConfig()
    {
        $this->config[0]['options'] = [
            'validation' => [
                '*' => ContactInputFilter::class
            ]
        ];
        $params = $this->config[0]['options'];
        $route = $this->prophesize(Route::class);
        $routeResult = RouteResult::fromRoute($route->reveal(), $params);
        $route->getName()->willReturn('contacts');
        $router = $this->getMockBuilder(ZendRouter::class)->getMock();
        $router->expects($this->any())
            ->method('match')
            ->willReturn($routeResult);
        $this->router = $router;
    }
}
