<?php

namespace api\components;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use api\components\QueryParamAuth;

class BaseController extends ActiveController
{
    public $userInfo;
    public $Token;

    public function init(){
        parent::init();
        $headers = Yii::$app->getRequest()->getHeaders();
        $Token = $headers->get('token');
        if($Token){$this->Token=$Token;}
        //$this->userInfo = json_decode(json_encode(Yii::$app->user->identity), true);var_dump($this->userInfo);exit;
    }

    //token验证
    public function behaviors() {
        return ArrayHelper::merge (parent::behaviors(), [
            'authenticator' => [
                'class' => QueryParamAuth::className() ,
                'tokenParam' => 'token',
                'optional' => [//过滤不需要验证Token的action
                    'login',
                    'signup',
                    'home',
                    'details',
                    'reply-list',
                    'reply-details',
                    'reply-details-list',
                    'list',
                    'video-list',
                    'announce-details',
                    'my',
                    //'signup-test'
                ],
            ]
        ] );
    }

    //自定义返回数据封装
    public static function __response($message='ok',$status=0,$data=[]){
        return [
            'message'=>$message,
            'status'=>$status,
            'data'=>$data,
        ];
    }

}
