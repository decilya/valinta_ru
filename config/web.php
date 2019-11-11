<?php

$params = require(__DIR__ . '/params.php');
$mailer = require(__DIR__ . '/mailer.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'formatter' => [
            'dateFormat'     => 'php:d-m-Y',
            'datetimeFormat' => 'php:d-m-Y в H:i:s',
            'timeFormat'     => 'php:H:i:s',
        ],
        'assetManager' => [
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'BNJ7wFaEEmkWVvCJwyvxAA5iopO4-ZXt',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Auth',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => $mailer,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [

                '/register' => '/site/register',
                '/request' => '/site/request',
                '/login' => '/site/login',
                '/recover' => '/site/recover',
                '/change-pass/<token:[\w\-]+>' => '/site/change-pass',
                '/change-pass' => '/site/change-pass',
                '/search' => '/site/index',
                '/more-results' => '/site/more-results',
                '/get-contacts' => '/site/get-contacts',


                '<controller:\w >/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:[\w\-]+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:[\w\-]+>' => '<controller>/<action>',

                '/user/accept-user/<id:\d+>/<anchor>' => '/user/accept-user',
                '/user/reject-user/<id:\d+>/<anchor>' => '/user/reject-user',

                [
                    'class' => 'app\components\rules\ContentRule'
                ],

            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['192.168.11.118', '192.168.11.33', '192.168.11.34'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['192.168.11.118', '192.168.11.33', '192.168.11.34'],
    ];
}


return $config;