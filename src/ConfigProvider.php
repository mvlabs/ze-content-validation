<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation;

use Mezzio\Container\ErrorResponseGeneratorFactory;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\DataExtractorChainFactory;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use ZE\ContentValidation\Extractor\OptionsExtractorFactory;
use ZE\ContentValidation\Extractor\ParamsExtractor;
use ZE\ContentValidation\Extractor\ParamsExtractorFactory;
use ZE\ContentValidation\Middleware\ValidationMiddleware;
use ZE\ContentValidation\Middleware\ValidationMiddlewareFactory;
use ZE\ContentValidation\Validator\ValidatorHandler;
use ZE\ContentValidation\Validator\ValidatorHandlerFactory;
use Laminas\InputFilter\InputFilterPluginManager;
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
                ErrorHandler::class => ErrorResponseGeneratorFactory::class,
            ],
            'aliases' => [
                'InputFilterManager' => InputFilterPluginManager::class,
            ],
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
                \Laminas\InputFilter\InputFilterAbstractServiceFactory::class,
            ],
        ];
    }
}
