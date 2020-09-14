<?php
return [
    'adminEmail' => 'admin@example.com',
    // token 有效期默认7天
    'user.apiTokenExpire' => 7*24*3600,
    'user.liketime' => 300,//用户点赞时间间隔 单位秒 Yii::$app->params['user.liketime']
    'sendsms_code_time' => 2*60,//发送验证码的有效时间 单位秒 Yii::$app->params['sendsms_code_time']
];
