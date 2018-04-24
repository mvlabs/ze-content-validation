<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */
namespace ZE\ContentValidation;

use LosMiddleware\ApiProblem;
use ZE\ContentValidation\Extractor\ParamsExtractor;
use ZE\ContentValidation\Extractor\ParamsExtractorFactory;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\DataExtractorChainFactory;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use ZE\ContentValidation\Extractor\OptionsExtractorFactory;
use ZE\ContentValidation\Middleware\ValidationMiddleware;
use ZE\ContentValidation\Middleware\ValidationMiddlewareFactory;
use ZE\ContentValidation\Validator\ValidatorHandler;
use ZE\ContentValidation\Validator\ValidatorHandlerFactory;
use Zend\Expressive\FinalHandler;
use Zend\InputFilter\InputFilterPluginManager;
use zf2timo\Bridge\Factory\InputFilterManagerFactory;

/**
 * Class ConfigProvider
 *
 * @package ZE\ContentValidation
 */
class ConfigProvider
{
    /**
     * @return mixed
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'input_filters' => $this->getInputFiltersConfig(),
        ];
    }

    /**
     * Provide default container dependency configuration.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'factories' => [
                ValidationMiddleware::class => ValidationMiddlewareFactory::class,
                ValidatorHandler::class => ValidatorHandlerFactory::class,
                InputFilterPluginManager::class => InputFilterManagerFactory::class,
                OptionsExtractor::class => OptionsExtractorFactory::class,
                ParamsExtractor::class => ParamsExtractorFactory::class,
                DataExtractorChain::class => DataExtractorChainFactory::class,
                ErrorHandler::class => ApiProblem\ErrorHandlerFactory::class,
                ApiProblem\ErrorResponseGenerator::class => ApiProblem\ErrorResponseGeneratorFactory::class,
            ],
            'aliases' => [
                'InputFilterManager' => InputFilterPluginManager::class,
            ]
        ];
    }

    /**
     * Provide default input filters configuration.
     *
     * @return array
     */
    public function getInputFiltersConfig()
    {
        return [
            'abstract_factories' => [
                \Zend\InputFilter\InputFilterAbstractServiceFactory::class,
            ]
        ];
    }
}
