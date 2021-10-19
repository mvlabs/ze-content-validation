<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Validator;

use Laminas\InputFilter\InputFilterInterface;

final class ValidationResult implements ValidationResultInterface
{
    /**
     * @var mixed[]
     */
    private array $rawValues;

    /**
     * @var mixed[]
     */
    private array $values;

    /**
     * @var array<string, string[]>
     */
    private array $messages;

    /**
     * @var null|string
     */
    private ?string $method;

    /**
     * ValidationResult constructor.
     *
     * @param array $rawValues
     * @param array $values
     * @param array $messages
     * @param null|string $method
     */
    public function __construct(
        array $rawValues,
        array $values,
        array $messages,
        $method = null
    ) {
        $this->rawValues = $rawValues;
        $this->values = $values;
        $this->messages = $messages;
        $this->method = $method;
    }

    public static function buildFromInputFilter(InputFilterInterface $inputFilter, $method)
    {
        $messages = [];

        if (!$inputFilter->isValid()) {
            foreach ($inputFilter->getInvalidInput() as $message) {
                $messages[$message->getName()] = $message->getMessages();
            }
        }

        // Return validation result
        return new self(
            $inputFilter->getRawValues(),
            $inputFilter->getValues(),
            $messages,
            $method
        );
    }

    /**
     * @inheritdoc
     */
    public function isValid()
    {
        return (count($this->messages) === 0);
    }

    /**
     * @inheritdoc
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @inheritdoc
     */
    public function getRawValues()
    {
        return $this->rawValues;
    }

    /**
     * @inheritdoc
     */
    public function getValues()
    {
        return $this->values;
    }
}
