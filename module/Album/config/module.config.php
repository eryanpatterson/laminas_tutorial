<?php 
namespace Album;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Router\Http\Segment;

return [
    
    'router' => [
        'routes' => [
            'album' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/album[/:action[/:id]]', // Action and id optional
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*', // Restrict to alphanumeric
                        'id' => '[0-9]+', // Restrict id to digits
                    ],
                    'defaults' => [
                        'controller' => Controller\AlbumController::class,
                        'action' => 'index',
                    ],
                ],
            ],
        ],    
    ],

    'view_manager' => [
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
            'track' => __DIR__ . '/../view',
        ],
    ],
];

?>