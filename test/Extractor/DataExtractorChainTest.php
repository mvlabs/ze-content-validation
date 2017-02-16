<?php

namespace ZETest\ContentValidation\Validator;

use PHPUnit\Framework\TestCase;
use ZE\ContentValidation\Extractor\BodyExtractor;
use ZE\ContentValidation\Extractor\DataExtractorChain;
use ZE\ContentValidation\Extractor\DataExtractorInterface;
use ZE\ContentValidation\Extractor\FileExtractor;
use ZE\ContentValidation\Extractor\QueryExtractor;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\UploadedFile;

class DataExtractorChainTest extends TestCase
{
    public function testGetDataFromRequestFromEmptyChain()
    {
        $dataExtractorChain = new DataExtractorChain([]);
        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([]);
        $actual = $dataExtractorChain->getDataFromRequest($request);


        self::assertCount(0, $actual);
    }

    public function testGetDataFromRequestDefaultExtraction()
    {
        $extractors = [
            $firstExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock(),
            $secondExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock()
        ];

        $dataExtractorChain = new DataExtractorChain($extractors);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([
                'Foo' => 'FooBar',
                'Fizz' => 'Buzz',
            ]);

        $firstExtractor->expects(self::any())->method('extractData')->will(self::returnValue([
            'Foo' => 'Bar',
            'Bar' => 'Foo',
        ]));

        $secondExtractor->expects(self::any())->method('extractData')->will(self::returnValue([
            'Foo' => 'FooBar',
            'Fizz' => 'Buzz',
        ]));

        $actual = $dataExtractorChain->getDataFromRequest($request);

        self::assertArraySubset(
            [
                'Foo' => 'FooBar',
                'Bar' => 'Foo',
                'Fizz' => 'Buzz',
            ],
            $actual
        );
    }

    public function testGetDataFromRequesExtractTraversable()
    {
        $extractors = [
            $firstExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock(),
            $secondExtractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock()
        ];

        $dataExtractorChain = new DataExtractorChain($extractors);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([
                'Foo' => 'FooBar',
                'Fizz' => 'Buzz',
            ]);

        $firstExtractor->expects(self::any())->method('extractData')->will(self::returnValue(new \ArrayIterator([
            'Foo' => [
                'Fizz' => 'Buzz',
            ],
        ])));

        $secondExtractor->expects(self::any())->method('extractData')->will(self::returnValue(new \ArrayIterator([
            'Foo' => [
                'Fizz' => 'Bar',
            ],
        ])));

        $actual = $dataExtractorChain->getDataFromRequest($request);

        self::assertArraySubset(
            [
                'Foo' => [
                    'Fizz' => 'Bar',
                ],
            ],
            $actual
        );
    }

    /**
     * //@expectedException ZE\ContentValidation\Exception\UnexpectedValueException
     */
    public function testGetDataFromRequestInvalidExtraction()
    {

        $extractors = [
            $extractor = $this->getMockBuilder(DataExtractorInterface::class)->getMock()
        ];

        $dataExtractorChain = new DataExtractorChain($extractors);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody([
                'Foo' => 'FooBar',
                'Fizz' => 'Buzz',
            ]);

        $extractor->expects(self::any())->method('extractData')->will(self::returnValue(new \stdClass()));

        $dataExtractorChain->getDataFromRequest($request);
    }

    public function testBodyExtractorExtractDataFromRequestOnPostAndIsOk()
    {
        $extractor = new BodyExtractor();
        $data = [
            'Foo' => 'FooBar',
            'Fizz' => 'Buzz',
        ];

            $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withParsedBody($data);

        $actual = $extractor->extractData($request);

        self::assertArraySubset(
            $data,
            $actual
        );
    }

    public function testQueryExtractorExtractDataFromRequestOnGetAndIsOk()
    {
        $extractor = new QueryExtractor();
        $data = [
            'Foo' => 'FooBar',
            'Fizz' => 'Buzz',
        ];

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('GET')
            ->withQueryParams($data);

        $actual = $extractor->extractData($request);

        self::assertArraySubset(
            $data,
            $actual
        );
    }

    public function testFileExtractorExtractDataFromRequestOnPostAndIsOk()
    {
        $extractor = new FileExtractor();
        $data = [
            'filename' => [
                'tmp_name' => '',
                'name' => '/tmp/12345678adf',
                'type' => 'text/plain',
                'size' => '10',
                'error' => null
            ]
        ];

        $uploadedFile = $this->prophesize(UploadedFile::class);

        $uploadedFile->getStream()->willReturn('');
        $uploadedFile->getClientFilename()->willReturn('/tmp/12345678adf');
        $uploadedFile->getClientMediaType()->willReturn('text/plain');
        $uploadedFile->getSize()->willReturn('10');
        $uploadedFile->getError()->willReturn(null);

        $request = ServerRequestFactory::fromGlobals()
            ->withMethod('POST')
            ->withUploadedFiles([
                'filename' => $uploadedFile->reveal()
            ]);

        $actual = $extractor->extractData($request);

        self::assertArraySubset(
            $data,
            $actual
        );
    }
}
