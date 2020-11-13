<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\AppConfig;

class AppConfigController extends BaseController
{
    public function init(){
        parent::init();
    }

    public $modelClass = 'api\models\AppConfig';

    /**
     *Time:2020/11/13 9:43
     *Author:始渲
     *Remark:APP闪屏图
     * @params:
     */
    public function actionSplashPic(){
        
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $AppConfig_rows=AppConfig::find()->select(['value','desc'])->where(['name'=>'SPLASH_PIC'])->asArray()->one();
        if($AppConfig_rows){
            return parent::__response('ok',0,$AppConfig_rows);
        }else{
            return parent::__response('暂无数据',0);
        }

    }

}
