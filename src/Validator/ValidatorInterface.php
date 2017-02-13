<?php

namespace ZE\ContentValidation\Validator;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ValidatorInterface
 * @package ZE\ContentValidation\Validator
 */
interface ValidatorInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $request);
}
