<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Extractor;

use Interop\Container\ContainerInterface;
use Mezzio\Router\RouterInterface;

/**
 * Class OptionsExtractorFactory
 *
 * @package ZE\ContentValidation\Extractor
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
class OptionsExtractorFactory
{
    public function __invoke(ContainerInterface $container)
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
