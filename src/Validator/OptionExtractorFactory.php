<?php
namespace ZE\ContentValidation\Validator;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class OptionExtractorFactory
 * @package StdLib\Helpers
 */
class OptionExtractorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new OptionsExtractor($container->get('config')['routes'], $container->get(RouterInterface::class));
    }
}
