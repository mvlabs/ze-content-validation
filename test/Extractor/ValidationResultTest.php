<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZETest\ContentValidation\Extractor;

use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use ZE\ContentValidation\Validator\ValidationResult;

class ValidationResultTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function itShouldProcessInvalidInputFiltersWithOnlyInputs(): void
    {
        $inputFilter = (new InputFilter())
            ->add((new Input())->setRequired(true), 'foo')
            ->add((new Input())->setRequired(true), 'bar');

        $inputFilter->setData([]);

        $validationResult = ValidationResult::buildFromInputFilter($inputFilter, 'dummy');

        self::assertEquals(
            [
                'foo' => [
                    'isEmpty' => 'Value is required and can\'t be empty',
                ],
                'bar' => [
                    'isEmpty' => 'Value is required and can\'t be empty',
                ],
            ],
            $validationResult->getMessages()
        );
    }

    /**
     * @test
     */
    public function itShouldProcessInvalidInputFiltersWithInputsAndInputFilters(): void
    {
        $inputFilter = (new InputFilter())
            ->add((new Input())->setRequired(true), 'foo')
            ->add(
                (new InputFilter())
                    ->add((new Input())->setRequired(true), 'nestedFooInBar'),
                'bar'
            );

        $inputFilter->setData([]);

        $validationResult = ValidationResult::buildFromInputFilter($inputFilter, 'dummy');

        self::assertEquals(
            [
                'foo' => [
                    'isEmpty' => 'Value is required and can\'t be empty',
                ],
                'bar' => [
                    'nestedFooInBar' => [
                        'isEmpty' => 'Value is required and can\'t be empty',
                    ],
                ],
            ],
            $validationResult->getMessages()
        );
    }
}
