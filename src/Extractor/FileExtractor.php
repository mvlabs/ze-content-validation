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
use Psr\Http\Message\UploadedFileInterface;

class FileExtractor implements DataExtractorInterface
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function extractData(ServerRequestInterface $request): array
    {
        $files = [];
        $uploadedFiles = $request->getUploadedFiles();

        if (count($uploadedFiles) !== 0) {
            foreach ($uploadedFiles as $key => $uploadedFile) {
                $files[$key] = $this->uploadedFileToArray($uploadedFile);
            }
        }

        return $files;
    }

    /**
     * @return array<string, mixed>
     */
    private function uploadedFileToArray(UploadedFileInterface $uploadedFile): array
    {
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $stream = $uploadedFile->getStream();

            return [
                'tmp_name' => $stream->getMetadata('uri'),
                'name' => $uploadedFile->getClientFilename(),
                'type' => $uploadedFile->getClientMediaType(),
                'size' => $uploadedFile->getSize(),
                'error' => $uploadedFile->getError(),
            ];
        }

        return [];
    }
}
