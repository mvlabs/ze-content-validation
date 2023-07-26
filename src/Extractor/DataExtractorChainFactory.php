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

use Psr\Container\ContainerInterface;

class DataExtractorChainFactory
{
    public function __invoke(ContainerInterface $container): DataExtractorChain
    {
        /** @var array<int, DataExtractorInterface> $extractors */
        $extractors = [
            $container->get(QueryExtractor::class),
            $container->get(BodyExtractor::class),
            $container->get(FileExtractor::class),
            $container->get(ParamsExtractor::class),
        ];

        return new DataExtractorChain($extractors);
    }
}
