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

/**
 * Class DataExtractorChainFactory
 *
 * @package ZE\ContentValidation\Extractor
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
class DataExtractorChainFactory
{
    /**
     * @param ContainerInterface $container
     * @return DataExtractorChain
     */
    public function __invoke(ContainerInterface $container)
    {
        $extractors = [
            new QueryExtractor(),
            new BodyExtractor(),
            new FileExtractor(),
            $container->get(ParamsExtractor::class)
        ];

        return new DataExtractorChain($extractors);
    }
}
