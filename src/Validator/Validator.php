<?php

namespace ZE\ContentValidation\Validator;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use Zend\Expressive\Router\RouterInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\Db\RecordExists;

/**
 * Validates the object
 * Class Validator
 * @package SchedulerApi\Validators
 */
class Validator implements ValidatorInterface
{
    /**
     * @var OptionsExtractor
     */
    private $optionsExtractor;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Request with parsed json
     * @var ServerRequestInterface
     */
    private $decoratedRequest;

    /**
     * The options extractor for extracting from the config
     * @param OptionsExtractor $optionsExtractor
     * @param RouterInterface $router
     */
    public function __construct(
        OptionsExtractor $optionsExtractor,
        RouterInterface $router
    ) {
        $this->optionsExtractor = $optionsExtractor;
        $this->router = $router;
    }


    /**
     * Validates the request
     * @param ServerRequestInterface $requestInterface
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $requestInterface)
    {
        //$this->decoratedRequest = new RequestValidator($requestInterface, $this->router);
        $this->decoratedRequest = $requestInterface;
        $validatorProvider = $this->getValidatorObject($this->decoratedRequest);

        if ($validatorProvider == null) {
            return true;
        } else {
            /*return new ValidationResult(
                $this->decoratedRequest->getParsedBody(),
                $validatorProvider,
                $this->decoratedRequest
            );*/
        }
    }

    /**
     * Checks an returns the validation object
     * or null otherwise
     * @param ServerRequestInterface $request
     * @return ValidationRulesInterface
     */
    private function getValidatorObject(ServerRequestInterface $request)
    {
        $routeConfig = $this->optionsExtractor->getOptionsForRequest($request);
        if (isset($routeConfig['validation'])) {
            return $this->getValidationObjectForRequest($request, $routeConfig['validation']);
        } else {
            // No associated validation
            return null;
        }
    }

    /**
     * Returns the validation object
     * @param ServerRequestInterface $request
     * @param array $validation
     * @return ValidationRulesInterface
     */
    private function getValidationObjectForRequest(ServerRequestInterface $request, array $validation)
    {
        $method = strtolower($request->getMethod());
        $validation = array_change_key_case($validation, CASE_LOWER);
        // Check if there is specific validation
        if (array_key_exists($method, $validation)) {
            return $this->instantiate($validation[$method], $request);
        } elseif (array_key_exists('*', $validation)) {
            return $this->instantiate($validation['*'], $request);
        } else {
            return null;
        }
    }

    /**
     * Instantiates the validator object provider class
     * @param $class
     * @param ServerRequestInterface $request
     * @return mixed
     * @throws ValidationClassNotExists
     */
    private function instantiate($class, ServerRequestInterface $request)
    {
        if (!class_exists($class)) {
            throw new ValidationClassNotExists("Class with name: " . $class . " does not exist");
        } else {
            return (new ReflectionClass($class))->newInstanceArgs([$request]);
        }
    }
}
