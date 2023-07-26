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

use Psr\Container\ContainerInterface;
use Laminas\InputFilter\InputFilterPluginManager;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\OptionsExtractor;

class ValidatorHandlerFactory
{
    public function __invoke(ContainerInterface $container): ValidatorHandler
    {
        return new ValidatorHandler(
            $container->get(OptionsExtractor::class),
            $container->get(DataExtractorChain::class),
            $container->get(InputFilterPluginManager::class)
        );
    }
}
