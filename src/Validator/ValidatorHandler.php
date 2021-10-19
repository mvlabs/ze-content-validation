<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Validator;

use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Exception\ValidationClassNotExists;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use Laminas\InputFilter\InputFilter;
use Laminas\InputFilter\InputFilterInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Class ValidatorHandler
 *
 * @package ZE\ContentValidation\Validator
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
class ValidatorHandler implements ValidatorInterface
{
    /**
     * @var OptionsExtractor
     */
    private $optionsExtractor;

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
     *
     * @param OptionsExtractor        $optionsExtractor
     * @param DataExtractorChain      $dataExtractorChain
     * @param ServiceLocatorInterface $inputFilterManager
     */
    public function __construct(
        OptionsExtractor $optionsExtractor,
        DataExtractorChain $dataExtractorChain,
        ServiceLocatorInterface $inputFilterManager
    ) {
        $this->optionsExtractor = $optionsExtractor;
        $this->dataExtractorChain = $dataExtractorChain;
        $this->inputFilterManager = $inputFilterManager;
    }


    /**
     * Validates the request
     *
     * @param  ServerRequestInterface $request
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $request)
    {
        $validatorProvider = $this->getValidatorObject($request);

        if ($validatorProvider instanceof InputFilterInterface) {
            $data = $this->dataExtractorChain->getDataFromRequest($request);
            $validatorProvider->setData($data);
            return ValidationResult::buildFromInputFilter($validatorProvider, $request->getMethod());
        }

        return true;
    }

    /**
     * Checks an returns the validation object
     * or null otherwise
     *
     * @param  ServerRequestInterface $request
     * @return InputFilter|null
     */
    private function getValidatorObject(ServerRequestInterface $request)
    {
        $routeValidationConfig = $this->optionsExtractor->getOptionsForRequest($request);

        if (isset($routeValidationConfig)) {
            $method = strtolower($request->getMethod());
            $validation = array_change_key_case($routeValidationConfig, CASE_LOWER);

            if (array_key_exists($method, $validation)) {
                return $this->getInputFilter($validation[$method]);
            } elseif (array_key_exists('*', $validation)) {
                return $this->getInputFilter($validation['*']);
            }
        }
        // No associated validation
        return null;
    }

    /**
     * @param $inputFilterService
     * @return InputFilter
     * @throws ValidationClassNotExists
     */
    private function getInputFilter($inputFilterService)
    {
        $inputFilter = $this->inputFilterManager->get($inputFilterService);

        if (! $inputFilter instanceof InputFilter) {
            throw new ValidationClassNotExists(
                sprintf(
                    'Listed input filter "%s" does not exist; cannot validate request',
                    $inputFilterService
                )
            );
        }

        return $this->inputFilterManager->get($inputFilterService);
    }
}
