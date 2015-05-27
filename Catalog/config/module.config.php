<?php

return array(
    'router'          => array(
        'routes' => array(
            'catalog' => array(
                'type'          => 'segment',
                'options'       => array(
                    'route'    => '/catalog/',
                    'defaults' => array(
                        'controller' => 'CatalogIndexController',
                        'action'     => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes'  => array(
                    'list'    => array(
                        'type'    => 'segment',
                        'options' => array(
                            'route'       => '[page/:page/]',
                            'defaults'    => array(
                                'controller' => 'CatalogIndexController',
                                'action'     => 'index',
                            ),
                            'constraints' => array(
                                'page' => '[1-9][0-9]*',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'CatalogIndexController'   => 'Catalog\Controller\IndexController',
            'CatalogFilterForm'        => 'Catalog\Form\CatalogFilter',
        ),
    ),
    'controllers'     => array(
        'factories' => array(
            'CatalogIndexController'   => 'Catalog\Controller\Factory\CategoryControllerFactory',
        ),
    ),
    'view_manager'    => array(
        'template_path_stack' => array(
            __DIR__.'/../view',
        ),
        'strategies'          => array(
            'ViewJsonStrategy',
        ),
    ),
);
