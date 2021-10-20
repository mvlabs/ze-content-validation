<?php
/**
 * ze-content-validation (https://github.com/func0der/ze-content-validation)
 *
 * @copyright Copyright (c) 2017 MVLabs(http://mvlabs.it)
 * @copyright Copyright (c) 2021 func0der
 * @license   MIT
 */

declare(strict_types=1);

namespace ZE\ContentValidation\Extractor;

use Mezzio\Router\Route;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;

class OptionsExtractor
{
    /**
     * @var array<string, array<string, string>>
     */
    private array $config;
    private RouterInterface $router;

    /**
     * @param array<string, array<string, string>> $config
     */
    public function __construct(array $config, RouterInterface $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * @return mixed[]
     */
    public function getOptionsForRequest(ServerRequestInterface $request): array
    {
        $matchedRoute = $this->router->match($request)->getMatchedRoute();

        if (! $matchedRoute instanceof Route) {
            return [];
        }

        foreach ($this->config as $routeName => $options) {
            if ($routeName === $matchedRoute->getName()) {
                return $options;
            }
        }

        return [];
    }
}
