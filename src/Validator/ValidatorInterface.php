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

use Psr\Http\Message\ServerRequestInterface;

interface ValidatorInterface
{
    /**
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $request);
}
