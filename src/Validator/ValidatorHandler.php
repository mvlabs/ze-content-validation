<?php

namespace ZE\ContentValidation\Validator;

use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Exception\ValidationClassNotExists;
use Zend\Expressive\Router\RouterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class Validator
 * @package ZE\ContentValidation\Validator
 */
class ValidatorHandler implements ValidatorInterface
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

    private $inputFilterManager;

    /**
     * @var array
     */
    protected $methodsWithoutBodies = [
        'GET',
        'HEAD',
        'OPTIONS',
        'DELETE',
    ];

    /**
     * The options extractor for extracting from the config
     * @param OptionsExtractor $optionsExtractor
     * @param RouterInterface $router
     * @param ServiceLocatorInterface $inputFilterManager
     */
    public function __construct(
        OptionsExtractor $optionsExtractor,
        RouterInterface $router,
        ServiceLocatorInterface $inputFilterManager
    )
    {
        $this->optionsExtractor = $optionsExtractor;
        $this->router = $router;
        $this->inputFilterManager = $inputFilterManager;
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
        if ($validatorProvider instanceof InputFilterInterface) {
            $postData = $this->getDataFromRequest($requestInterface);
            $validatorProvider->setData($postData);
            return $validatorProvider;
        }

        return true;
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
        $method = strtolower($request->getMethod());
        $validation = array_change_key_case($routeConfig['validation'], CASE_LOWER);

        if (isset($routeConfig['validation'])) {
            if (array_key_exists($method, $validation)) {
                return $this->getInputFilter($validation[$method]);
            } elseif (array_key_exists('*', $validation)) {
                return $this->getInputFilter($validation['*']);
            }
        }
        // No associated validation
        return null;
    }

    private function getInputFilter($inputFilterService)
    {
        $inputFilter = $this->inputFilterManager->get($inputFilterService);
        if (!$inputFilter instanceof InputFilter) {
            throw new ValidationClassNotExists(sprintf('Listed input filter "%s" does not exist; cannot validate request', $inputFilterService));
        }

        return $this->inputFilterManager->get($inputFilterService);
    }

    private function getDataFromRequest($requestInterface)
    {
        $method = $requestInterface->getMethod();
        $data = in_array($method, $this->methodsWithoutBodies)
            ? $requestInterface->getQueryParams()
            : $requestInterface->getParsedBody();

        $files = $requestInterface->getUploadedFiles();
        if (0 < count($files)) {
            $data = ArrayUtils::merge($data, $files->toArray(), true);
        }

        return $data;
    }
}
