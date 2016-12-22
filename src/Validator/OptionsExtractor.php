<?php
namespace ZE\ContentValidation\Validator;

use ArrayObject;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Application;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 * Class OptionsExtractor
 * @package SchedulerApi\Middleware
 */
class OptionsExtractor
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var RouterInterface $route
     */
    private $router;

    /**
     * @codeCoverageIgnore
     * @param array $config
     * @param RouterInterface $router
     */
    public function __construct(array $config, RouterInterface $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return array []
     */
    public function getOptionsForRequest(ServerRequestInterface $request)
    {
        /**
         * @var RouteResult $routeMatch
         */
        $routePath = $this->router->match($request)->getMatchedRouteName();

        foreach ($this->config as $route) {
            if ($route['name'] === $routePath) {

                return isset($route['options']) ? $route['options'] : [];
            }
        }
        return [];
    }

    /**
     * Get all routes definitions
     * @return mixed []
     */
    public function getAll()
    {
        return $this->config;
    }

    /**
     * Get a sanitize route definition
     * @return array
     */
    public function getAllSanitize()
    {
        return array_map(function ($item) {
            return [
                "name" => $item['name'],
                "path" => $item['path'],
                "allowed_methods" => isset($item['allowed_methods']) ? $item['allowed_methods'] : ['GET'],
            ];
        }, $this->getAll());
    }
}
