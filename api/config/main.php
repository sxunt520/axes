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
                            'POST site' => 'site/test',
                            'POST loading' => 'loading/home',
                            'POST members' => 'member/send-sms',
                            'POST members' => 'member/mobile-login',
                            'POST members' => 'member/third-login',
                            'POST members' => 'member/signup',
                            'POST members' => 'member/login',
                            'POST members' => 'member/user-profile',
                            'POST members' => 'member/test',
                            'POST members' => 'member/my',
                            'POST members' => 'member/edit-nickname',
                            'POST members' => 'member/edit-signature',
                            'POST members' => 'member/follower-list',
                            'POST members' => 'member/fans-list',
                            'POST members' => 'member/like-list',
                            'POST members' => 'member/following',
                            'POST members' => 'member/upload-photo',
                            'POST members' => 'member/my-reply-list',
                            'POST members' => 'member/my-reply-details',
                            'POST members' => 'member/real-authentication',
                            'POST members' => 'member/third-bind-mobile',
                            'POST members' => 'member/mobile-bind-third',
                            'POST members' => 'member/my-comment-list',
                            'POST storys' => 'story/home',
                            'POST storys' => 'story/like',
                            'POST storys' => 'story/get-likes',
                            'POST storys' => 'story/details',
                            'POST storys' => 'story/collect',
                            'POST storys' => 'story/video-list',
                            'POST storys' => 'story/announce-details',
                            'POST storys' => 'story/share',
                            'POST story-comments' => 'story-comment/home',
                            'POST story-comments' => 'story-comment/list',
                            'POST story-comments' => 'story-comment/details',
                            'POST story-comments' => 'story-comment/add',
                            'POST story-comments' => 'story-comment/edit',
                            'POST story-comments' => 'story-comment/get-comment-img',
                            'POST story-comments' => 'story-comment/like',
                            'POST story-comments' => 'story-comment/get-likes',
                            'POST story-comments' => 'story-comment/share',
                            'POST story-comments' => 'story-comment/add-screen-comment',
                            'POST story-comments' => 'story-comment/screen-comment-list',
                            'POST story-comment-replys' => 'story-comment-reply/like',
                            'POST story-comment-replys' => 'story-comment-reply/get-likes',
                            'POST story-comment-replys' => 'story-comment-reply/add',
                            'POST story-comment-replys' => 'story-comment-reply/reply-list',
                            'POST story-comment-replys' => 'story-comment-reply/reply-details',
                            'POST story-comment-replys' => 'story-comment-reply/reply-details-list',
                            'POST members' => 'member/report-add',
                            'POST members' => 'member/shield-add',
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
//                $response->data = [
//                        'success' => $response->isSuccessful,
//                        'response' => $response->data,
//                    ];
//                    $response->statusCode = 200;
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
