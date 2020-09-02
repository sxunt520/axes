<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'language' => 'zh-CN',
    'charset' => 'utf-8',
    'defaultRoute' => '/site/index',
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ],
        'v2' => [
            'class' => 'api\modules\v2\Module',
        ],
	],
    'components' => [
         'user' => [ 
            'identityClass' => 'common\models\Member',
            'enableAutoLogin' => true,
            'enableSession'=>false
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
            //'enableStrictParsing' => true,//这个为true 为验证后面是否为复数有s
            'rules' => [
                            // 'class' => 'yii\rest\UrlRule',
                            // 'controller' => ['v1/user'],
                            // 'extraPatterns' => [
                            //     'POST login' => 'login',
                            //     'POST signup' => 'signup',
                            //     'GET user-profile' => 'user-profile',
                            //   //'GET signup-test' => 'signup-test',
                            // ],
                            'POST members' => 'member/signup',
                            'POST members' => 'member/login',
                            'POST members' => 'member/user-profile',
                            'POST members' => 'member/test',
                            'POST articles' => 'article/list',
                            'POST storys' => 'story/home',
                            'POST storys' => 'story/like',
                            'POST storys' => 'story/get-likes',
                            'POST storys' => 'story/details',
                            'POST story-comments' => 'story-comment/home',
                            'POST story-comments' => 'story-comment/details',
                     ],
          ],
//            'response' => [
//                'class' => 'yii\web\Response',
//                'on beforeSend' => function ($event) {
//                        $response = $event->sender;
//                        $response->data = [
//                            'code' => $response->getStatusCode(),
//                            'data' => $response->data,
//                            'message' => $response->statusText
//                        ];
//                        $response->format = yii\web\Response::FORMAT_JSON;
//                    },
//            ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                //if ($response->data !== null && !empty(Yii::$app->request->get('suppress_response_code'))) {//当 suppress_response_code 作为 GET 参数传递时，上面的代码 将重新按照自己定义的格式响应（无论失败还是成功
                if ($response->data !== null) {
                $response->data = [
                        'success' => $response->isSuccessful,
                        'response' => $response->data,
                    ];
                    $response->statusCode = 200;
                    $response->format = yii\web\Response::FORMAT_JSON;
                }
            },
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',//site里配置actions class 
            //'class' => 'api\components\ExceptionHandler',
        ],

    ],
    'params' => $params,
];
