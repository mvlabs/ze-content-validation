<?php

namespace ZE\ContentValidation\Validator;

use Interop\Container\ContainerInterface;

class ValidationMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     * @return ValidationMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ValidationMiddleware(
            $container->get(Validator::class)
        );
    }
}
