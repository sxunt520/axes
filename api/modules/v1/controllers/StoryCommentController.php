<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\Story;
use api\models\StoryTag;
use api\models\StoryComment;
use api\models\StoryCommentImg;

class StoryCommentController extends BaseController
{
    public function init(){
        parent::init();
    }
    
    public $modelClass = 'api\models\StoryComment';

    /**
     * 评论热度Top首页
     */
    public function actionHome(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("page")||!Yii::$app->request->POST("pagenum")){
            return parent::__response('参数错误!',(int)-2);
        }
    	$page = (int)Yii::$app->request->post('page');//当前页
    	$pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
    	if ($page < 1) $page = 1;
    	if ($pagenum < 1) $pagenum = 5;
    	
         $StoryComment_rows=StoryComment::find()
            ->select(['id','story_id','title','content','from_uid','created_at','comment_img_id','heart_val','is_plot','likes'])
            ->andWhere(['=', 'is_show', 1])
            ->orderBy(['heart_val' => SORT_DESC,'id'=>SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
         
      //标签、图片
        foreach ($StoryComment_rows as $k=>$v){
            $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['story_id']])->asArray()->all();
            if($StoryTag_rows) $StoryComment_rows[$k]['tags']=$StoryTag_rows;
            $StoryCommentImg=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $v['comment_img_id']])->asArray()->one();
            if($StoryCommentImg)$StoryComment_rows[$k]['comment_img']=$StoryCommentImg;
        }

        return parent::__response('ok',0,$StoryComment_rows);
        
    }

    /**
     * 评论详情页内容
     */
    public function actionDetails(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $id = (int)Yii::$app->request->post('id');

        $StoryComment_row=StoryComment::find()
            ->andWhere(['=', 'id', $id])
            ->asArray()
            ->one();

          //标签
        //$StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $id])->asArray()->all();
        //if($StoryTag_rows) $StoryComment_row['tags']=$StoryTag_rows;

        //评论图
        if($StoryComment_row['comment_img_id']>0){
            $StoryCommentImg_rows=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $StoryComment_row['comment_img_id']])->asArray()->all();
            if($StoryCommentImg_rows) $StoryComment_row['comment_img']=$StoryCommentImg_rows;
        }

        return parent::__response('ok',0,$StoryComment_row);

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
//             $_response['message']='ok';
//             $_response['code']=(int)0;
//             $_response['data']['likes']=(int)Yii::$app->cache->get($story_id);
//             return $_response;
            return parent::__response('ok',0,['likes'=>Yii::$app->cache->get($story_id)]);
        }else{//缓存中都没有，初次访问然后去库中取
            $_response=array();
            $_response=self::__likes($story_id);
            if (!empty($StoryLikeLog_model->error)){
                $_response['message']=$StoryLikeLog_model->error;
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
                'code'=>(int)-1,
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
