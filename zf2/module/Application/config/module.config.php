<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Inventory',
                        'action'     => 'index',
                    ),
                ),
            ),
            'memberhome' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/memberhome',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Inventory',
                        'action'     => 'listCustomers',
                    ),
                ),
            ),
            
            
            
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Inventory',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'console' => array(
		'router' => array(
			'routes' => array(
				'cronroute' => array(
					'options' => array(
						'route'    => 'sendtransactions',
						'defaults' => array(
								'__NAMESPACE__' => 'Application\Controller',
								'controller'    => 'Inventory',
								'action'        => 'sendDailyTransactionsReport',
                        
							
						)
					)
				),
				'import' => array(
					'options' => array(
						'route'    => 'import <filename> ',
						'defaults' => array(
								'__NAMESPACE__' => 'Application\Controller',
								'controller'    => 'Inventory',
								'action'        => 'importControls',
                        
							
						)
					)
				),
				
 			)
		)
	),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Inventory' => 'Application\Controller\InventoryController',
            
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),

    'doctrine' => array(

    
        // 2) standard configuration for the ORM from https://github.com/doctrine/DoctrineORMModule
        // http://www.jasongrimes.org/2012/01/using-doctrine-2-in-zend-framework-2/
        // ONLY THIS IS REQUIRED IF YOU USE Doctrine in the module
        'driver' => array(
            // defines an annotation driver with two paths, and names it `my_annotation_driver`
//            'my_annotation_driver' => array(
            'Application_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    // __DIR__ . '/../module/CsnUser/src/CsnUser/Entity' // 'path/to/my/entities',
                    __DIR__ . '/../src/Application/Entity',
                ),
            ),

            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default' => array(
                'drivers' => array(
                    // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                    // 'My\Namespace' => 'my_annotation_driver'
                    // 'CsnUser' => 'my_annotation_driver'
                    'Application\Entity' => 'Application_driver',
                )
            )
        )
    ),
);
