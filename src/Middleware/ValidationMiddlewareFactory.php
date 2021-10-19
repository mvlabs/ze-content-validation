<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Middleware;

use Interop\Container\ContainerInterface;
use ZE\ContentValidation\Validator\ValidatorHandler;
use Laminas\ProblemDetails\ProblemDetailsResponseFactory;

/**
 * Class ValidationMiddlewareFactory
 *
 * @package ZE\ContentValidation\Middleware
 * @author  Diego Drigani<d.drigani@mvlabs.it>
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
            $container->get(ValidatorHandler::class),
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
