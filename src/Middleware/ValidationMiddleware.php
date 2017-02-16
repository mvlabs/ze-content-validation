<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */
namespace ZE\ContentValidation\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ZE\ContentValidation\Exception\ValidationException;
use ZE\ContentValidation\Validator\ValidationResult;
use ZE\ContentValidation\Validator\ValidatorHandler;
use Zend\Stratigility\MiddlewareInterface;

/**
 * Class ValidationMiddleware
 *
 * @package ZE\ContentValidation\Middleware
 * @author  Diego Drigani<d.drigani@mvlabs.it>
 */
class ValidationMiddleware implements MiddlewareInterface
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

    /**
     * Validates the request
     *
     * @param      Request       $request
     * @param      Response      $response
     * @param      callable|null $out
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        /**
         * @var ValidationResult $validationResult
         */
        $validationResult = $this->validator->validate($request);

        if ($validationResult instanceof ValidationResult && ! $validationResult->isValid()) {
            $request = $request->withAttribute('validationResult', $validationResult);

            $validationException = new ValidationException(
                'Failed Validation',
                422,
                null,
                [],
                ['validation_messages' => $validationResult->getMessages()]
            );


            return $out($request, $response->withStatus(422), $validationException);
        }

        return $out($request, $response);
    }
}
