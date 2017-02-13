<?php

namespace ZE\ContentValidation\Validator;

use Interop\Container\ContainerInterface;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use Zend\Expressive\Router\RouterInterface;


/**
 * Class ValidatorHandlerFactory
 * @package ZE\ContentValidation\Validator
 */
class ValidatorHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return ValidatorHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ValidatorHandler(
            $container->get(OptionsExtractor::class),
            $container->get(DataExtractorChain::class),
            $container->get(RouterInterface::class),
            $container->get('InputFilterManager')
        );
    }
}
