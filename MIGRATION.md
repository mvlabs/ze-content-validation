# Migration

## 2.0 to 3.0

* Added Return Type to `\ZE\ContentValidation\Extractor\DataExtractorChain::getDataFromRequest(): array`
* Added Return Type to `\ZE\ContentValidation\Extractor\ParamsExtractor::getDataFromRequest(): array`
* Added Return Type to `\ZE\ContentValidation\Extractor\QueryExtractor::getDataFromRequest(): array`
* Make sure your DI Container implements `\Psr\Container\ContainerInterface` (`Interop\Container\ContainerInterface`
  extends Psr's implementation)
* This package does not require `laminas/laminas-i18n` anymore. Make sure your project states it in your dependencies
  explicitly if you need it.

## mvlabs/ze-content-validation:2.0 to func0der/ze-content-validation

* Use PHP 7.4 or above
* Use Laminas instead Zend
* Use Mezzio instead of Expressive

