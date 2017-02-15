<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license MIT
 */
namespace ZE\ContentValidation\Validator;

use Zend\InputFilter\InputFilterInterface;

/**
 * Class ValidationResult
 *
 * @package ZE\ContentValidation\Validator
 * @author Diego Drigani<d.drigani@mvlabs.it>
 */
final class ValidationResult implements ValidationResultInterface
{
    /**
     * @var array
     */
    private $rawValues;

    /**
     * @var array
     */
    private $values;

    /**
     * @var array
     */
    private $messages;

    /**
     * @var null|string
     */
    private $method;

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
