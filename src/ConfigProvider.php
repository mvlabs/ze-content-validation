<?php
/**
 * ze-content-validation (https://github.com/mvlabs/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @license   MIT
 */
namespace ZE\ContentValidation;

use Zend\Config\Factory;
use Zend\Stdlib\Glob;

/**
 * Class ConfigProvider
 *
 * @package ZE\ContentValidation
 */
class ConfigProvider
{
    /**
     * @return mixed
     */
    public function __invoke()
    {
        return Factory::fromFiles(Glob::glob(__DIR__ . '/../config/{,*.}config.php', Glob::GLOB_BRACE));
    }
}
