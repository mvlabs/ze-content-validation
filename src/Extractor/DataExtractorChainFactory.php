<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license MIT
 */
namespace ZE\ContentValidation\Extractor;

use Interop\Container\ContainerInterface;
use ZE\ContentValidation\Extractor\BodyExtractor;
use ZE\ContentValidation\Extractor\FileExtractor;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use ZE\ContentValidation\Extractor\QueryExtractor;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class DataExtractorChainFactory
 *
 * @package ZE\ContentValidation\Extractor
 * @author Diego Drigani <d.drigani@mvlabs.it>
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
            new FileExtractor()
        ];

        return new DataExtractorChain($extractors);
    }
}
