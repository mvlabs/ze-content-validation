# Changelog

# 3.1

### Changed

* Make it possible to have nested input filter definitions

### Removed

* PHP 8.0 support removed


# 3.0

### Added

* `\ZE\ContentValidation\Extractor\QueryExtractor`, `\ZE\ContentValidation\Extractor\BodyExtractor`
  and `\ZE\ContentValidation\Extractor\FileExtractor` are now available as services.
* Improved types in PHPDocs
* Vagrantfile for easy local testing
* Licensing information

### Changed

* Support PHP7.4+ only
* Migrate to Mezzio/Laminas
* Type hints & return types added

### Removed
* Dependency on `laminas/laminas-i18n` dropped - was never used anyway.

### Updated

* `laminas/laminas-inputfilter` is now required in `^2.19` instead of just `^2.8`
* Updated dev dependencies `phpstan` (and plugins), `phpunit`, `php_codesniffer`
* Use `\Psr\Container\ContainerInterface` directly instead of deprecated `\Interop\Container\ContainerInterface`
