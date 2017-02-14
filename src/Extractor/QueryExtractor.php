<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license MIT
 */
namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\RequestInterface;

/**
 * Class QueryExtractor
 * @package ZE\ContentValidation\Extractor
 * @author Diego Drigani <d.drigani@mvlabs.it>
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
