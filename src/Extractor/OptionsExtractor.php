<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */
namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class OptionsExtractor
 *
 * @package ZE\ContentValidation\Extractor
 * @author  Diego Drigani <d.drigani@mvlabs.it>
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
     * OptionsExtractor constructor.
     *
     * @param array           $config
     * @param RouterInterface $router
     */
    public function __construct(array $config, RouterInterface $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return array
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
     * @return array
     */
    public function getAll()
    {
        return $this->config;
    }


    /**
     * @return array
     */
    public function getAllSanitize()
    {
        return array_map(
            function ($item) {
                return [
                    "name" => $item['name'],
                    "path" => $item['path'],
                    "allowed_methods" => isset($item['allowed_methods']) ? $item['allowed_methods'] : ['GET'],
                ];
            },
            $this->getAll()
        );
    }
}
