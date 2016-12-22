<?php

namespace ZE\ContentValidation\Validator;

use Psr\Http\Message\ServerRequestInterface;

interface ValidatorInterface
{
    /**
     * @param ServerRequestInterface $requestInterface
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $requestInterface);
}
