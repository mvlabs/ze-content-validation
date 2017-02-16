<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */
namespace ZE\ContentValidation\Extractor;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class OptionsExtractorFactory
 *
 * @package ZE\ContentValidation\Extractor
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
class OptionsExtractorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new OptionsExtractor($container->get('config')['routes'], $container->get(RouterInterface::class));
    }
}
