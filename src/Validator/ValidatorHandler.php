<?php

namespace ZE\ContentValidation\Validator;

use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Exception\ValidationClassNotExists;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use Zend\Expressive\Router\RouterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
     * @var DataExtractorChain
     */
    private $dataExtractorChain;

    /**
     * @var ServiceLocatorInterface
     */
    private $inputFilterManager;


    /**
     * ValidatorHandler constructor.
     * @param OptionsExtractor $optionsExtractor
     * @param DataExtractorChain $dataExtractorChain
     * @param RouterInterface $router
     * @param ServiceLocatorInterface $inputFilterManager
     */
    public function __construct(
        OptionsExtractor $optionsExtractor,
        DataExtractorChain $dataExtractorChain,
        RouterInterface $router,
        ServiceLocatorInterface $inputFilterManager
    ) {
        $this->optionsExtractor = $optionsExtractor;
        $this->dataExtractorChain = $dataExtractorChain;
        $this->router = $router;
        $this->inputFilterManager = $inputFilterManager;
    }


    /**
     * Validates the request
     * @param ServerRequestInterface $request
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $request)
    {
        $validatorProvider = $this->getValidatorObject($request);
        if ($validatorProvider instanceof InputFilterInterface) {
            $data = $this->dataExtractorChain->getDataFromRequest($request);
            $validatorProvider->setData($data);
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
}
