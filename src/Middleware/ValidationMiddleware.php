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

use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZE\ContentValidation\Validator\ValidationResult;
use ZE\ContentValidation\Validator\ValidatorHandler;

class ValidationMiddleware implements ServerMiddlewareInterface
{
    private ProblemDetailsResponseFactory $problemDetailsFactory;
    private ValidatorHandler $validator;

    public function __construct(
        ValidatorHandler $validator,
        ProblemDetailsResponseFactory $problemDetailsFactory
    ) {
        $this->validator = $validator;
        $this->problemDetailsFactory = $problemDetailsFactory;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $validationResult = $this->validator->validate($request);

        if (is_bool($validationResult)) {
            return $handler->handle($request);
        }

        if (!$validationResult->isValid()) {
            return $this->problemDetailsFactory->createResponse(
                $request,
                422,
                'Failed Validation',
                '',
                '',
                ['errors' => $validationResult->getMessages()]
            );
        }

        $request = $request->withParsedBody($validationResult->getValues());

        return $handler->handle($request);
    }
}
