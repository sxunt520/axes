<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-autoheroes',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'autoheroes\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
	        'enablePrettyUrl' => true,
	        'showScriptName' => false,
	        'rules' => [
	        [
	        'pattern' => '<id:\d+>',
	        'route' => 'article/view',
	        'suffix' => '.html'
	        		],
	        		'user/<id:\d+>' => '/user',
	        		'tag/<name:\S+>' => '/article/tag'
	        				],
        	],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
    ],
    'as ThemeBehavior' => \autoheroes\behaviors\ThemeBehavior::className(),//主题适配
    'params' => $params,
];
