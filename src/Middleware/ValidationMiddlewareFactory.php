<?php

namespace ZE\ContentValidation\Middleware;

use Interop\Container\ContainerInterface;
use ZE\ContentValidation\Middleware\ValidationMiddleware;
use ZE\ContentValidation\Validator\ValidatorHandler;

class ValidationMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     * @return ValidationMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ValidationMiddleware(
            $container->get(ValidatorHandler::class)
        );
    }
}
