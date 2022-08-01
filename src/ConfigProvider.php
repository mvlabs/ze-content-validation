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

use Laminas\ServiceManager\Factory\InvokableFactory;
use ZE\ContentValidation\Extractor\BodyExtractor;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\DataExtractorChainFactory;
use ZE\ContentValidation\Extractor\FileExtractor;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use ZE\ContentValidation\Extractor\OptionsExtractorFactory;
use ZE\ContentValidation\Extractor\ParamsExtractor;
use ZE\ContentValidation\Extractor\ParamsExtractorFactory;
use ZE\ContentValidation\Extractor\QueryExtractor;
use ZE\ContentValidation\Middleware\ValidationMiddleware;
use ZE\ContentValidation\Middleware\ValidationMiddlewareFactory;
use ZE\ContentValidation\Validator\ValidatorHandler;
use ZE\ContentValidation\Validator\ValidatorHandlerFactory;

class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
            'input_filters' => $this->getInputFiltersConfig(),
        ];
    }

    /**
     * @return array<string, array<class-string, class-string>>
     */
    public function getDependencyConfig(): array
    {
        return [
            'factories' => [
                QueryExtractor::class => InvokableFactory::class,
                BodyExtractor::class => InvokableFactory::class,
                FileExtractor::class => InvokableFactory::class,
                ValidationMiddleware::class => ValidationMiddlewareFactory::class,
                ValidatorHandler::class => ValidatorHandlerFactory::class,
                OptionsExtractor::class => OptionsExtractorFactory::class,
                ParamsExtractor::class => ParamsExtractorFactory::class,
                DataExtractorChain::class => DataExtractorChainFactory::class,
            ],
        ];
    }

    /**
     * @return array<string, class-string[]>
     */
    public function getInputFiltersConfig(): array
    {
        return [
            'abstract_factories' => [
                \Laminas\InputFilter\InputFilterAbstractServiceFactory::class,
            ],
        ];
    }
}
