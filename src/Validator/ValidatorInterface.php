<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Validator;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ValidatorInterface
 *
 * @package ZE\ContentValidation\Validator
 * @author  Diego Drigani <d.drigani@mvlabs.it>
 */
interface ValidatorInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return bool|ValidationResult
     */
    public function validate(ServerRequestInterface $request);
}
