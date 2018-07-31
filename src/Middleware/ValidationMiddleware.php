<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface as ServerMiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ZE\ContentValidation\Exception\ValidationException;
use ZE\ContentValidation\Validator\ValidationResult;
use ZE\ContentValidation\Validator\ValidatorHandler;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

/**
 * Class ValidationMiddleware
 *
 * @package ZE\ContentValidation\Middleware
 * @author  Diego Drigani<d.drigani@mvlabs.it>
 */
class ValidationMiddleware implements ServerMiddlewareInterface
{
    /**
     * @var ProblemDetailsResponseFactory
     */
    private $problemDetailsFactory;
    /**
     * @var ValidatorHandler
     */
    private $validator;

    /**
     * ValidationMiddleware constructor.
     *
     * @param ValidatorHandler $validator
     * @param ProblemDetailsResponseFactory $problemDetailsFactory
     */
    public function __construct(
        ValidatorHandler $validator,
        ProblemDetailsResponseFactory $problemDetailsFactory
    ) {
        $this->validator = $validator;
        $this->problemDetailsFactory = $problemDetailsFactory;
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /**
         * @var ValidationResult $validationResult
         */
        $validationResult = $this->validator->validate($request);

        if ($validationResult instanceof ValidationResult && ! $validationResult->isValid()) {
            return $this->problemDetailsFactory->createResponse(
                $request,
                422,
                'Failed Validation',
                '',
                '',
                ['errors' => $validationResult->getMessages()]
            );
        }

        if ($validationResult instanceof ValidationResult) {
            $request = $request->withParsedBody($validationResult->getValues());
        }

        return $handler->handle($request);
    }
}
