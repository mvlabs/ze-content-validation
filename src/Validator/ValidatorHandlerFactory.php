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

use Interop\Container\ContainerInterface;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\OptionsExtractor;

/**
 * Class ValidatorHandlerFactory
 *
 * @package ZE\ContentValidation\Validator
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
class ValidatorHandlerFactory
{
    /**
     * @param ContainerInterface $container
     * @return ValidatorHandler
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ValidatorHandler(
            $container->get(OptionsExtractor::class),
            $container->get(DataExtractorChain::class),
            $container->get('InputFilterManager')
        );
    }
}
