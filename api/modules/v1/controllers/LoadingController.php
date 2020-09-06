<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\Loading;

class LoadingController extends BaseController
{
    public function init(){
        parent::init();
    }

    public $modelClass = 'api\models\Loading';

    /**
     * Loading 欢迎页几张图
     */
    public function actionHome(){

        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 7;

        $Loading_rows=Loading::find()
            ->select(['id','title','img_url','order_by'])
            ->andWhere(['=', 'is_show', 1])
            ->orderBy(['order_by' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();

        return parent::__response('ok',0,$Loading_rows);

    }

}
