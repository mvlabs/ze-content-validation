<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */
namespace ZE\ContentValidation\Middleware;

use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface as ServerMiddlewareInterface;
use LosMiddleware\ApiProblem\ErrorResponseGenerator;
use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Exception\ValidationException;
use ZE\ContentValidation\Validator\ValidationResult;
use ZE\ContentValidation\Validator\ValidatorHandler;
use Zend\Diactoros\Response\JsonResponse;

/**
 * Class ValidationMiddleware
 *
 * @package ZE\ContentValidation\Middleware
 * @author  Diego Drigani<d.drigani@mvlabs.it>
 */
class ValidationMiddleware implements ServerMiddlewareInterface
{
    /**
     * @var ErrorResponseGenerator
     */
    private $errorResponseGenerator;
    /**
     * @var ValidatorHandler
     */
    private $validator;

    /**
     * ValidationMiddleware constructor.
     *
     * @param ValidatorHandler $validator
     * @param ErrorResponseGenerator $errorResponseGenerator
     */
    public function __construct(ValidatorHandler $validator, ErrorResponseGenerator $errorResponseGenerator)
    {
        $this->validator = $validator;
        $this->errorResponseGenerator = $errorResponseGenerator;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var ValidationResult $validationResult
         */
        $validationResult = $this->validator->validate($request);

        if ($validationResult instanceof ValidationResult && ! $validationResult->isValid()) {
            $e = new ValidationException(
                'Failed Validation',
                422,
                null,
                [],
                ['errors' => $validationResult->getMessages()]
            );

            return ($this->errorResponseGenerator)($e, $request, new JsonResponse([]));
        }

        return $delegate->process($request);
    }
}
