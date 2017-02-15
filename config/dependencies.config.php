<?php

return [
    'input_filters' => [
        'abstract_factories' => [
            \Zend\InputFilter\InputFilterAbstractServiceFactory::class,
        ],
    ],
    'dependencies' => [
        'invokables' => [

        ],
        'factories' => [
            \Zend\InputFilter\InputFilterPluginManager::class => \zf2timo\Bridge\Factory\InputFilterManagerFactory::class,
            \ZE\ContentValidation\Extractor\OptionsExtractor::class => \ZE\ContentValidation\Extractor\OptionsExtractorFactory::class,
            \ZE\ContentValidation\Extractor\DataExtractorChain::class => \ZE\ContentValidation\Extractor\DataExtractorChainFactory::class,
            \Zend\Expressive\FinalHandler::class => LosMiddleware\ApiProblem\ApiProblem::class,
        ],
        'aliases' => [
            'InputFilterManager' => \Zend\InputFilter\InputFilterPluginManager::class,
        ]

    ],
];
