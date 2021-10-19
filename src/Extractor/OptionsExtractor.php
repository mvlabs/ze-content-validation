<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Mezzio\Router\RouteResult;
use Mezzio\Router\RouterInterface;

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
        $matchedRoute = $this->router->match($request)->getMatchedRoute();
        foreach ($this->config as $routeName => $options) {
            if ($routeName === $matchedRoute->getName()) {
                return isset($options) ? $options : [];
            }
        }

        return [];
    }

    /**
     * @return array
     */
    private function getAll()
    {
        return $this->config;
    }
}
