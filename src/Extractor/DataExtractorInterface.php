<?php

namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\RequestInterface;

interface DataExtractorInterface
{
    public function extractData(RequestInterface $request);
}