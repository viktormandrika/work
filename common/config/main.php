<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
//        'view' => [
//            'theme' => [
//                'pathMap' => [
//                    //'@dektrium/user/views' => '@backend/views'
//                ],
//            ],
//        ],
    ],
    'modules' => [
        'User' => [
            'class' => 'dektrium\user\Module'
        ],
        'rbac' => 'dektrium\rbac\RbacWebModule',
    ],
];