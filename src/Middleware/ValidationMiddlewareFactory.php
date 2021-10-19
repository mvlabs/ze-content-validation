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
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use ZE\ContentValidation\Validator\ValidatorHandler;

class ValidationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): ValidationMiddleware
    {
        return new ValidationMiddleware(
            $container->get(ValidatorHandler::class),
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
