<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license MIT
 */
namespace ZE\ContentValidation\Middleware;

use Interop\Container\ContainerInterface;
use ZE\ContentValidation\Validator\ValidatorHandler;

/**
 * Class ValidationMiddlewareFactory
 *
 * @package ZE\ContentValidation\Middleware
 * @author Diego Drigani<d.drigani@mvlabs.it>
 */
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
