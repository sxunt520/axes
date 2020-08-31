<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\Story;

class StoryController extends BaseController
{
    public function init(){
        parent::init();
    }
    
    public $modelClass = 'api\models\Story';

    /**
     * 故事首页推荐内容
     */
    public function actionHome(){
    	
    	$page = (int)Yii::$app->request->post('page');//当前页
    	$pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
    	if ($page < 1) $page = 1;
    	if ($pagenum < 1) $pagenum = 1;
    	
         $Article_model=Story::find()
	        ->select(['id','title','intro','type','cover_url','video_url','created_at','updated_at','next_updated_at','current_chapters','total_chapters'])
	        ->andWhere(['=', 'is_show', 1])
	        ->andWhere(['=', 'type', 1])
	        ->orderBy(['id' => SORT_DESC])
	        ->offset($pagenum * ($page - 1))
	        ->limit($pagenum)
	        ->all();
         
        return $Article_model;
        
    }

    
}
