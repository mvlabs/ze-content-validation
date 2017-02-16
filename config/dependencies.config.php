<?php

use LosMiddleware\ApiProblem\ApiProblemHandlerFactory;
use ZE\ContentValidation\Extractor\DataExtractorChainFactory;
use ZE\ContentValidation\Extractor\OptionsExtractorFactory;
use zf2timo\Bridge\Factory\InputFilterManagerFactory;

return [
    'input_filters' => [
        'abstract_factories' => [
            \Zend\InputFilter\InputFilterAbstractServiceFactory::class,
        ],
    ],
    'dependencies' => [
        'invokables' => [
            LosMiddleware\ApiProblem\ApiProblem::class => LosMiddleware\ApiProblem\ApiProblem::class,
        ],
        'factories' => [
            \Zend\InputFilter\InputFilterPluginManager::class => InputFilterManagerFactory::class,
            \ZE\ContentValidation\Extractor\OptionsExtractor::class => OptionsExtractorFactory::class,
            \ZE\ContentValidation\Extractor\DataExtractorChain::class => DataExtractorChainFactory::class,
            \Zend\Expressive\FinalHandler::class => ApiProblemHandlerFactory::class,
        ],
        'aliases' => [
            'InputFilterManager' => \Zend\InputFilter\InputFilterPluginManager::class,
        ]
    ]
];
