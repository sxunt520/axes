<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\Story;
use api\models\StoryTag;
use api\models\StoryImg;

use yii\web\NotFoundHttpException;
use api\components\library\UserException;

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
    	//throw new NotFoundHttpException('标签不存在');
    	//throw new UserException(['code'=>400,'message'=>'xxxxx','errorCode'=>2000]);
    	//Yii::$app->response->statusCode = 300;
    	//throw new \yii\web\HttpException(400, 'xxxxxxxxxxxxxxxxxxxx',4000);
    	
    	//     	$redis = Yii::$app->redis;
    	//     	$key = 'username';
    	//     	if($val = $redis->get($key)){
    	// 			var_dump($val);exit;
    	// 		} else {
    	// 			$redis->set($key, 'marko');
    	// 			$redis->expire($key, 5);
    	// 		}
    	
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
	        ->asArray()
	        ->all();
         
         //标签、多图    
        foreach ($Article_model as $k=>$v){
        	$StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['id']])->asArray()->all();
        	$StoryImg_rows=StoryImg::find()->select(['id','img_url','img_text'])->where(['story_id' => $v['id']])->asArray()->all();
        	if($StoryTag_rows) $Article_model[$k]['tags']=$StoryTag_rows;
        	if($StoryImg_rows) $Article_model[$k]['iamges']=$StoryImg_rows;
        }
         
        return $Article_model;
        
    }

    
}
