<?php
namespace Payment;

return array(
    'controllers' => array(
        'invokables' => array(
            'Payment\Controller\Payment' => 'Payment\Controller\IndexController',
            
        ),
    ),
    // The following section is new and should be added to your file
        'router' => array(
        'routes' => array(
            'payment' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/payment',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Payment\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
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
                                '__NAMESPACE__' => 'Payment\Controller',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
//    'router' => array(
//        'routes' => array(
//            'payment' => array(
//                'type' => 'segment',
//                'options' => array(
//                    'route' => '/payment[/:action][/:key]',
//                    'constraints' => array(
//                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
//                        'key' => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
//                    ),
//                    'defaults' => array(
//                        'controller' => 'Payment\Controller\Payment',
//                        'action' => 'payment',
//                    ),
//                ),
//            ),
//        ),
//    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'payment' => __DIR__ . '/../view',
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