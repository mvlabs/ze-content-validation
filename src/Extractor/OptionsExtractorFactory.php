<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Extractor;

use Laminas\InputFilter\InputFilter;
use Psr\Container\ContainerInterface;
use Mezzio\Router\RouterInterface;

class OptionsExtractorFactory
{
    public function __invoke(ContainerInterface $container): OptionsExtractor
    {
        /** @var array<string, array<string, class-string<InputFilter>>> $validationConfig */
        $validationConfig = $container->get('config')['ze-content-validation'] ?? [];

        return new OptionsExtractor(
            $validationConfig,
            $container->get(RouterInterface::class)
        );
    }
}
