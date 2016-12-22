<?php

namespace ZE\ContentValidation\Validator;

use Zend\Expressive\Router\RouteResult;
use Zend\Expressive\Router\RouterInterface as Router;
use Zend\Expressive\Router\RouterInterface;
use Zend\Stratigility\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ValidationMiddleware implements MiddlewareInterface
{
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @codeCoverageIgnore
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validates the request
     * @param Request $request
     * @param Response $response
     * @param callable|null $out
     * @inheritdoc
     */
    public function __invoke(Request $request, Response $response, callable $out = null)
    {
        /**
         * @var ValidationResult $validationResult
         */
        $validationResult = $this->validator->validate($request);
        echo __CLASS__. __FUNCTION__;
        /*if (!is_bool($validationResult) && $validationResult->notValid()) {
            $validationException = new ValidationFailedException('Validation Failed');
            $validationException->setValidationResult($validationResult);
            throw $validationException;
        } else*/ if (!is_bool($validationResult)) {    // Valid
            return $out($request, $response);
        } else {
            return $out($request, $response);
        }
    }
}
