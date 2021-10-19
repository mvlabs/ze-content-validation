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

use Psr\Http\Message\ServerRequestInterface;

class QueryExtractor implements DataExtractorInterface
{
    /**
     * @return mixed
     */
    public function extractData(ServerRequestInterface $request)
    {
        return $request->getQueryParams();
    }
}
