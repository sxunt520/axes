<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\Story;
use api\models\StoryTag;
use api\models\StoryImg;
use api\models\StoryLikeLog;

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
    	
         $Story_rows=Story::find()
	        ->select(['id','title','intro','type','cover_url','video_url','created_at','likes'])
	        ->andWhere(['=', 'is_show', 1])
	        ->andWhere(['=', 'type', 1])
	        ->orderBy(['id' => SORT_DESC])
	        ->offset($pagenum * ($page - 1))
	        ->limit($pagenum)
	        ->asArray()
	        ->all();
         
      //标签、多图
        foreach ($Story_rows as $k=>$v){
            $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['id']])->asArray()->all();
            $StoryImg_rows=StoryImg::find()->select(['id','img_url','img_text'])->where(['story_id' => $v['id']])->asArray()->all();
            if($StoryTag_rows) $Story_rows[$k]['tags']=$StoryTag_rows;
            //if($StoryImg_rows) $Article_model[$k]['iamges']=$StoryImg_rows;
        }

        return parent::__response('ok',0,$Story_rows);
        
    }

    /**
 * 故事详情页内容
 */
    public function actionDetails(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $id = (int)Yii::$app->request->post('id');

        $Story_row=Story::find()
            ->select(['id','title','intro','type','cover_url','video_url','created_at','updated_at','next_updated_at','current_chapters','total_chapters','likes'])
            ->andWhere(['=', 'id', $id])
            ->asArray()
            ->one();

        //标签
        $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $id])->asArray()->all();
        if($StoryTag_rows) $Story_row['tags']=$StoryTag_rows;
        //多图
        $StoryImg_rows=StoryImg::find()->select(['id','img_url','img_text'])->where(['story_id' => $id])->asArray()->all();
        if($StoryImg_rows) $Story_row['iamges']=$StoryImg_rows;

        //人气值计算规则：人气值外显代表游戏观看数+评论+转发的虚拟数值总和，计算规则，1次观看=10点人气，一条评论=50点人气，一次转发=100点人气
        //公告标签处
        //最热评论处
        //宣传视频组

        return parent::__response('ok',0,$Story_row);

    }

    /**
     *点赞
     */
    public function actionLike(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $story_id=Yii::$app->request->POST("story_id");
        $user_id=Yii::$app->request->POST("user_id");
        if(!isset($story_id)||!isset($user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        $StoryLikeLog_model = new StoryLikeLog();

        $result=$StoryLikeLog_model->apiLike($story_id,$user_id);//数据库里去更新点赞数，存入缓存

        if ($result && Yii::$app->cache->exists($story_id)){
            return parent::__response('ok',0,['likes'=>Yii::$app->cache->get($story_id)]);
        }else{//缓存中都没有，初次访问然后去库中取
            $_response=array();
            $_response=self::__likes($story_id);
            if (!empty($StoryLikeLog_model->error)){
                $_response['message']=$StoryLikeLog_model->error;
                $_response['status']=(int)-1;
            }
            return $_response;
        }

    }

    /**
     *获取点赞条数
     */
    public function actionGetLikes(){
        $story_id=Yii::$app->request->post('story_id');
        if(!isset($story_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        if (Yii::$app->cache->exists($story_id)){
            $likes=Yii::$app->cache->get($story_id);
//            return [
//                'message'=>'ok',
//                'code'=>(int)0,
//                'data'=>['likes'=>(int)$likes],
//            ];
            return parent::__response('ok',0,['story_id'=>$story_id,'likes'=>(int)$likes]);
        }else{
            return self::__likes($story_id);
        }
    }


    //点赞数据库里提取，存入缓存中返回
    private static function __likes($story_id){
        $content=Story::find()->where(['id'=>$story_id])->select(['id','likes'])->asArray()->one();
        if (!$content){
            return [
                'message'=>'故事不存在',
                'status'=>(int)-1,
            ];
        }
        Yii::$app->cache->set($story_id,(int)$content['likes']);
//        return [
//            'message'=>'ok',
//            'code'=>(int)0,
//            'data'=>['likes'=>(int)$content['likes']],
//        ];
        return parent::__response('ok',0,['likes'=>(int)$content['likes']]);
    }

    
}