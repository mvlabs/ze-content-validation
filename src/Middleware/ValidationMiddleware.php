<?php

namespace ZE\ContentValidation\Middleware;


use LosMiddleware\ApiProblem\Exception\ApiException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ZE\ContentValidation\Exception\ValidationException;
use ZE\ContentValidation\Validator\ValidationResult;
use ZE\ContentValidation\Validator\ValidatorHandler;
use Zend\InputFilter\InputFilterInterface;
use Zend\Stratigility\MiddlewareInterface;


class ValidationMiddleware implements MiddlewareInterface
{
    /**
     * @var ValidatorHandler
     */
    private $validator;

    /**
     * @codeCoverageIgnore
     * @param ValidatorHandler $validator
     */
    public function __construct(ValidatorHandler $validator)
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
         * @var InputFilterInterface $inputFilter
         */
        $inputFilter = $this->validator->validate($request);

        if ($inputFilter instanceof InputFilterInterface  && !$inputFilter->isValid()) {

            $request = $request->withAttribute('inputFilter', $inputFilter);

            $validationException = new ValidationException(
                'Failed Validation',
                406,
                null,
                [],
                ['validation_messages' => $inputFilter->getMessages()]
            );


            return $out($request, $response->withStatus(406), $validationException);
        }

        return $out($request, $response);
    }
}
