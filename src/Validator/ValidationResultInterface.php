<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Validator;

/**
 * Interface ValidationResultInterface
 *
 * @package ZE\ContentValidation\Validator
 * @author  Diego Drigani<d.drigani@mvlabs.it>
 */
interface ValidationResultInterface
{
    /**
     * Check if the validation was successful
     *
     * If there are no validation messages set, the validation result object is considered valid.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Get validation messages
     *
     * @return array
     */
    public function getMessages();

    /**
     * Get the raw input values
     *
     * @return array
     */
    public function getRawValues();

    /**
     * Get the filtered input values
     *
     * @return array
     */
    public function getValues();
}
