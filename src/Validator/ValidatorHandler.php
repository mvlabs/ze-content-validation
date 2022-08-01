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

use Laminas\InputFilter\InputFilter;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Exception\ValidationClassNotExists;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\OptionsExtractor;

class ValidatorHandler implements ValidatorInterface
{
    private OptionsExtractor $optionsExtractor;
    private DataExtractorChain $dataExtractorChain;
    private ServiceLocatorInterface $inputFilterManager;

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
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $request)
    {
        $validatorProvider = $this->getValidatorObject($request);

        if ($validatorProvider instanceof InputFilter) {
            $data = $this->dataExtractorChain->getDataFromRequest($request);
            $validatorProvider->setData($data);

            return ValidationResult::buildFromInputFilter($validatorProvider, $request->getMethod());
        }

        return true;
    }

    /**
     * Checks and returns the validation object or null otherwise.
     */
    private function getValidatorObject(ServerRequestInterface $request): ?InputFilter
    {
        $routeValidationConfig = $this->optionsExtractor->getOptionsForRequest($request);

        if (count($routeValidationConfig) !== 0) {
            $method = strtolower($request->getMethod());
            $validation = array_change_key_case($routeValidationConfig, CASE_LOWER);

            if (array_key_exists($method, $validation)) {
                return $this->getInputFilter($validation[$method]);
            }

            if (array_key_exists('*', $validation)) {
                return $this->getInputFilter($validation['*']);
            }
        }

        // No associated validation
        return null;
    }

    /**
     * @param class-string<InputFilter> $inputFilterService
     *
     * @throws ValidationClassNotExists
     */
    private function getInputFilter(string $inputFilterService): InputFilter
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

        return $inputFilter;
    }
}
