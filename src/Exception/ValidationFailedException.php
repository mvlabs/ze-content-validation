<?php

namespace ZE\ContentValidation\Exception\Validator;

use Exception;

/**
 * Validation Failed Exception
 *
 * @author Diego Drigani <d.drigani@mvlabs.it>
 * @license MIT
 */
class ValidationFailedException extends Exception implements ExceptionInterface
{
    /**
     * @var ValidationResultInterface
     */
    private $validationResult;
    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     * @inheritdoc
     */
    public function __construct($message = "", $code = 406, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return ValidationResultInterface
     */
    public function getValidationResult()
    {
        return $this->validationResult;
    }

    /**
     * @param ValidationResultInterface $validationResult
     */
    public function setValidationResult($validationResult)
    {
        $this->validationResult = $validationResult;
    }
}
