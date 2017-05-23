<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */
namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\RequestInterface;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class ParamsExtractor
 *
 * @package ZE\ContentValidation\Extractor
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
class ParamsExtractor implements DataExtractorInterface
{
    /**
     * @var RouterInterface $route
     */
    private $router;

    /**
     * OptionsExtractor constructor.
     *
     * @param RouterInterface $router
     * @internal param array $config
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function extractData(RequestInterface $request)
    {
        $routeResult = $this->router->match($request);
        return $routeResult->getMatchedParams();
    }
}
