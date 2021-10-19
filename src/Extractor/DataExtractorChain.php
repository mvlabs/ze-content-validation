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

use Psr\Http\Message\RequestInterface;
use ZE\ContentValidation\Exception\UnexpectedValueException;
use Laminas\Stdlib\ArrayUtils;

/**
 * Class DataExtractorChain
 *
 * @package ZE\ContentValidation\Extractor
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
class DataExtractorChain
{

    /**
     * @var array DataExtractorInterfacece
     */
    protected $extractors = [];

    /**
     * ExtractorChain constructor.
     *
     * @param array $extractors
     */
    public function __construct(array $extractors)
    {
        $this->extractors = $extractors;
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getDataFromRequest(RequestInterface $request)
    {
        $result = [];
        $dataSets = array_map(
            function (DataExtractorInterface $extractor) use ($request) {
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
