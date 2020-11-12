<?php

namespace backend\controllers;

use Yii;
use common\models\AppConfig;

class AppConfigController extends \yii\web\Controller
{

    public function actions() {
        return [

            //截图上传
            'crop'=>[
                'class' => 'common\widgets\avatar\CropAction',
                'config'=>[
                    //main.js 中改 aspectRatio: 9 / 16,//纵横比
                    'bigImageWidth' => '720',     //大图默认宽度
                    'bigImageHeight' => '1280',    //大图默认高度
                    'middleImageWidth'=> '360',   //中图默认宽度
                    'middleImageHeight'=> '640',  //中图图默认高度
                    'smallImageWidth' => '180',    //小图默认宽度
                    'smallImageHeight' => '320',   //小图默认高度
                    //头像上传目录（注：目录前不能加"/"）
                    'uploadPath' => '../../api/web/uploads',
                ]
            ]

        ];
    }

    public function actionSplashPic()
    {
        //return $this->render('splash-pic');

        $model = AppConfig::find()->where(['name'=>'SPLASH_PIC'])->one();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '更新成功');
        }

        return $this->render('splash-pic', [
            'model' => $model,
        ]);

    }


}
