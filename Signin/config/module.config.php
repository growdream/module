<?php
namespace Signin;
return array(
     'controllers' => array(
         'invokables' => array(
             'Signin\Controller\Signin' => 'Signin\Controller\SigninController',
         ),
     ),
      // The following section is new and should be added to your file
  'router' => array(
         'routes' => array(
             'signin' => array(
                 'type'    => 'segment',
                 'options' => array(
                     'route'    => '/signin[/:action][/:key]',
                     'constraints' => array(
                         'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                         'key'     => '[a-zA-Z0-9][a-zA-Z0-9_-]*',
                         //'id'     => '[a-zA-Z0-9-]+',
                     ),
                     'defaults' => array(
                         'controller' => 'Signin\Controller\Signin',
                         'action'     => 'signin',
                     ),
                 ),
             ),
         ),
     ),

     'view_manager' => array(
         'template_path_stack' => array(
             'signin' => __DIR__ . '/../view',
         ),
          'strategies' => array (            // Add
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
         'authentication' => array(
            'orm_default' => array(
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'Signin\Entity\Signin',
                'identity_property' => 'user_id',
                'credential_property' => 'password',
                 'credential_callable' => 'Signin\Service\UserService::verifyHashedPassword',
           

            ),
        ),
    )
 );