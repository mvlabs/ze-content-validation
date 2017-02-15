<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license MIT
 */
namespace ZE\ContentValidation\Extractor;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class FileExtractor
 *
 * @package ZE\ContentValidation\Extractor
 * @author Diego Drigani <d.drigani@mvlabs.it>
 */
class FileExtractor implements DataExtractorInterface
{
    /**
     * @param RequestInterface $request
     * @return array
     */
    public function extractData(RequestInterface $request)
    {
        $files = [];
        $uploadedFiles = $request->getUploadedFiles();

        if (!empty($uploadedFiles)) {
            foreach ($uploadedFiles as $key => $uploadedFile) {
                $files[$key] = $this->uploadedFileToArray($uploadedFile);
            }
        }
        return $files;
    }

    /**
     * @param UploadedFileInterface $uploadedFile
     * @return array
     */
    public function uploadedFileToArray(UploadedFileInterface $uploadedFile)
    {
        if (!$uploadedFile->getError()) {
            $stream = $uploadedFile->getStream();
            return [
                'tmp_name' => ($stream) ? $stream->getMetadata('uri') : '',
                'name' => $uploadedFile->getClientFilename(),
                'type' => $uploadedFile->getClientMediaType(),
                'size' => $uploadedFile->getSize(),
                'error' => $uploadedFile->getError()
            ];
        }

        return [];
    }
}
