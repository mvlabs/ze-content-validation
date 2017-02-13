<?php
namespace ZE\ContentValidation\Extractor;

use Interop\Container\ContainerInterface;
use ZE\ContentValidation\Extractor\BodyExtractor;
use ZE\ContentValidation\Extractor\FileExtractor;
use ZE\ContentValidation\Extractor\OptionsExtractor;
use ZE\ContentValidation\Extractor\QueryExtractor;
use Zend\Expressive\Router\RouterInterface;

/**
 * Class OptionExtractorFactory
 * @package StdLib\Helpers
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
