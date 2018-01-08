<?php
namespace Dashboard;

return array(
    'controllers' => array(
        'invokables' => array(
            'Dashboard\Controller\Dashboard' => 'Dashboard\Controller\DashboardController',
            'Dashboard\Controller\User' => 'Dashboard\Controller\UserController',
            'Dashboard\Controller\Product' => 'Dashboard\Controller\ProductController',
        ),
    ),
    // The following section is new and should be added to your file
        'router' => array(
        'routes' => array(
            'dashboard' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/dashboard',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Dashboard\Controller',
                        'controller' => 'Dashboard',
                        'action' => 'dashboard',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                '__NAMESPACE__' => 'Dashboard\Controller',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
//    'router' => array(
//        'routes' => array(
//            'dashboard' => array(
//                'type' => 'segment',
//                'options' => array(
//                    'route' => '/dashboard[/:action][/:key]',
//                    'constraints' => array(
//                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'key' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'Dashboard\Controller\Dashboard',
//                        'action' => 'dashboard',
//                    ),
//                ),
//            ),
//        ),
//    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'dashboard' => __DIR__ . '/../view',
        ),
        'strategies' => array(// Add
            // this
            'ViewJsonStrategy' // line
        )
    ),
   
            // Doctrine config
            'doctrine' => array(
                'driver' => array(
                    __NAMESPACE__ . '_driver' => array(
                        'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                        'cache' => 'array',
                        'paths' => array(__DIR__ . '/../src/' . __NAMESPACE__ . '/Entity')
                    ),
                    'orm_default' => array(
                        'drivers' => array(
                            __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                        )
                    )
                ),
            )
        );