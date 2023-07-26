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

/**
 * @phpstan-type MessagesArray array<array<array<string[]|string>|string>>
 */
interface ValidationResultInterface
{
    /**
     * Check if the validation was successful
     *
     * If there are no validation messages set, the validation result object is considered valid.
     */
    public function isValid(): bool;

    /**
     * Get validation messages
     *
     * @return MessagesArray
     */
    public function getMessages(): array;

    /**
     * Get the raw input values
     *
     * @return mixed[]
     */
    public function getRawValues(): array;

    /**
     * Get the filtered input values
     *
     * @return mixed[]
     */
    public function getValues(): array;
}
