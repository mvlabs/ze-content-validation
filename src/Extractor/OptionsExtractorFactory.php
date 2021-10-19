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

use Interop\Container\ContainerInterface;
use Mezzio\Router\RouterInterface;

class OptionsExtractorFactory
{
    public function __invoke(ContainerInterface $container): OptionsExtractor
    {
        $validationConfig = $container->get('config')['ze-content-validation'] ?? [];

        return new OptionsExtractor(
            $validationConfig,
            $container->get(
                RouterInterface::class
            )
        );
    }
}
