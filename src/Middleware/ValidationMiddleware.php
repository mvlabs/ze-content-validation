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
use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Exception\ValidationException;
use ZE\ContentValidation\Validator\ValidationResult;
use ZE\ContentValidation\Validator\ValidatorHandler;


/**
 * Class ValidationMiddleware
 *
 * @package ZE\ContentValidation\Middleware
 * @author  Diego Drigani<d.drigani@mvlabs.it>
 */
class ValidationMiddleware  implements ServerMiddlewareInterface
{
    /**
     * @var ValidatorHandler
     */
    private $validator;

    /**
     * ValidationMiddleware constructor.
     *
     * @param ValidatorHandler $validator
     */
    public function __construct(ValidatorHandler $validator)
    {
        $this->validator = $validator;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        /**
         * @var ValidationResult $validationResult
         */
        $validationResult = $this->validator->validate($request);

        if ($validationResult instanceof ValidationResult && ! $validationResult->isValid()) {
            throw new ValidationException(
                'Failed Validation',
                422,
                null,
                [],
                ['validation_messages' => $validationResult->getMessages()]
            );
        }

        return $delegate->process($request);
    }
}
