<?php

namespace ZE\ContentValidation\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Interop\Container\ContainerInterface;

use Zend\Expressive\Router\RouterInterface;

/**
 * Instantiates the validator
 * Class ValidatorFactory
 */
class ValidatorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        echo 'popio';
        var_dump($container->get('InputFilterManager'));

        return new Validator(
            $container->get(OptionsExtractor::class),
            $container->get(RouterInterface::class)
        );
    }
}
