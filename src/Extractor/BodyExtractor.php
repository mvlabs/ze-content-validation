<?php

namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\RequestInterface;

/**
 * Class BodyExtractor
 * @package ZE\ContentValidation\Extractor
 */
class BodyExtractor implements DataExtractorInterface
{
    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function extractData(RequestInterface $request)
    {
        return $request->getParsedBody();
    }
}
