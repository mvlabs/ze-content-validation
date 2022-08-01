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

use Laminas\Stdlib\ArrayUtils;
use Psr\Http\Message\ServerRequestInterface;
use ZE\ContentValidation\Exception\UnexpectedValueException;

class DataExtractorChain
{
    /**
     * @var array<int, DataExtractorInterface>
     */
    protected array $extractors = [];

    /**
     * @param array<int, DataExtractorInterface> $extractors
     */
    public function __construct(array $extractors)
    {
        $this->extractors = $extractors;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDataFromRequest(ServerRequestInterface $request): array
    {
        $result = [];
        $dataSets = array_map(
            static function (DataExtractorInterface $extractor) use ($request): array {
                $data = $extractor->extractData($request);

                if ($data instanceof \Traversable) {
                    $data = iterator_to_array($data);
                }

                if (! is_array($data)) {
                    throw new UnexpectedValueException(
                        sprintf(
                            'Data Extractor `%s` returned a `%s` instead of an `array`',
                            get_class($extractor),
                            is_object($data) ? get_class($data) : gettype($data)
                        )
                    );
                }

                return $data;
            },
            $this->extractors
        );

        foreach ($dataSets as $data) {
            $result = ArrayUtils::merge($result, $data);
        }

        return $result;
    }
}
