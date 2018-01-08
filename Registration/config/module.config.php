<?php
namespace Registration;
return array(
     'controllers' => array(
         'invokables' => array(
             'Registration\Controller\Registration' => 'Registration\Controller\RegistrationController',
             'Registration\Controller\Product' => 'Registration\Controller\ProductController',
             
         ),
     ),
      // The following section is new and should be added to your file
/*  'router' => array(
         'routes' => array(
             'registration' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/registration[/:action][/:qval]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'qval'     => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                     ),
                     'defaults' => array(
                         'controller' => 'Registration\Controller\Registration',
                         'action'     => 'registration',
                     ),
                 ),
             ),
         ),
     ), */
    //
    // The following section is new and should be added to your file
        'router' => array(
        'routes' => array(
            'registration' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/registration',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Registration\Controller',
                        'controller' => 'Registration',
                        'action' => 'registration',
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
                                '__NAMESPACE__' => 'Registration\Controller',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),

//    'view_manager' => array(
//        'display_not_found_reason' => true,
//        'display_exceptions' => true,
//        'doctype' => 'HTML5',
//        'not_found_template' => 'error/404',
//        'exception_template' => 'error/index',
//        'template_map' => array(
//            'layout/layout' => __DIR__ . '/../view/layout/registration.phtml',
//            'registraion/registraion/registraion' => __DIR__ . '/../view/registraion/registraion/registraion.phtml',
//        ),
//        'template_path_stack' => array(
//            __DIR__ . '/../view',
//        ),
//    ),
     
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

            
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
        )
    )
 );