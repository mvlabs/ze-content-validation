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

/**
 * @phpstan-import-type MessagesArray from ValidationResultInterface
 */
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
     * @var MessagesArray
     */
    private array $messages;

    /**
     * @var null|string
     */
    private ?string $method;

    /**
     * ValidationResult constructor.
     *
     * @param mixed[] $rawValues
     * @param mixed[] $values
     * @param MessagesArray $messages
     */
    public function __construct(
        array $rawValues,
        array $values,
        array $messages,
        ?string $method
    ) {
        $this->rawValues = $rawValues;
        $this->values = $values;
        $this->messages = $messages;
        $this->method = $method;
    }

    public static function buildFromInputFilter(InputFilterInterface $inputFilter, string $method): self
    {
        $messages = [];

        if (! $inputFilter->isValid()) {
            foreach ($inputFilter->getInvalidInput() as $name => $message) {
                $messages[$name] = $message->getMessages();
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

    public function isValid(): bool
    {
        return (count($this->messages) === 0);
    }

    /**
     * @return MessagesArray
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @return mixed[]
     */
    public function getRawValues(): array
    {
        return $this->rawValues;
    }

    /**
     * @return mixed[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }
}
