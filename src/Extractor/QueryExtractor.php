<?php

namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\RequestInterface;

/**
 * Class QueryExtractor
 * @package ZE\ContentValidation\Extractor
 */
class QueryExtractor implements DataExtractorInterface
{
    /**
     * @param RequestInterface $request
     * @return mixed
     */
    public function extractData(RequestInterface $request)
    {
        return $request->getQueryParams();
    }
}
