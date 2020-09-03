<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\Story;
use api\models\StoryTag;
use api\models\StoryComment;
use api\models\StoryCommentImg;
use api\models\StoryCommentReply;
use api\models\StoryCommentReplyLikeLog;

class StoryCommentReplyController extends BaseController
{
    public function init(){
        parent::init();
    }

    public $modelClass = 'api\models\StoryCommentReply';

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
        if(!$StoryComment_rows){
            return parent::__response('获取失败',(int)-1);
        }
         
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

        //多少人赞过
        //回复的评论

        return parent::__response('ok',0,$StoryComment_row);

    }

    /**
     * 发布评论
     */
    public function actionAdd(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("comment_img_id")||!Yii::$app->request->POST("title")||!Yii::$app->request->POST("content")||!Yii::$app->request->POST("from_uid")){
            return parent::__response('参数错误!',(int)-2);
        }
        $story_id = (int)Yii::$app->request->post('story_id');//故事id
        $comment_img_id = (int)Yii::$app->request->post('comment_img_id');//评论图id

        //先看故事是否存在
        $Story_Model=Story::findOne($story_id);
        if(!$Story_Model){
            return parent::__response('故事不存在!',(int)-1);
        }
        //看选择的图片comment_img_id是否故事下面的图片
        $StoryCommentImg=StoryCommentImg::findOne($comment_img_id);
        if(!$StoryCommentImg||(int)$StoryCommentImg->story_id!=$story_id){
            return parent::__response('所传入的图片参数图片不存在，或者参数不是故事所属图片组!',(int)-1);
        }

        $story_comment_model=new StoryComment();
        $story_comment_model->story_id = $story_id;//故事id
        $story_comment_model->comment_img_id = $comment_img_id;//评论图id
        $story_comment_model->title = Yii::$app->request->post('title');//标题
        $story_comment_model->content = Yii::$app->request->post('content');//内容
        $story_comment_model->is_plot = Yii::$app->request->post('is_plot');//是否包含剧透 1是 0否
        $story_comment_model->from_uid = Yii::$app->request->post('from_uid');//评论用户id
        //$story_comment_model->created_at=time();

        //验证保存
        $isValid = $story_comment_model->validate();
        if ($isValid) {
            $r=$story_comment_model->save();
            if($r){
                $comment_id=$story_comment_model->id;
                return parent::__response('评论成功',(int)0,['comment_id'=>$comment_id]);
            }else{
                return parent::__response('评论失败!',(int)-1);
            }
        }else{
            return parent::__response('参数错误!',(int)-2);
        }


    }

    /**
     * 获取评论图片组
     */
    public function actionGetCommentImg(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("page")||!Yii::$app->request->POST("pagenum")){
            return parent::__response('参数错误!',(int)-2);
        }
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        $story_id = (int)Yii::$app->request->post('story_id');//故事id

        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 8;

        $StoryCommentImg_rows=StoryCommentImg::find()
            ->andWhere(['=', 'story_id', $story_id])
            ->orderBy(['id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if($StoryCommentImg_rows){
            return parent::__response('ok', 0, $StoryCommentImg_rows);
        }else{
            return parent::__response('获取失败',(int)-1);
        }

    }

    /**
     *回复评论点赞
     */
    public function actionLike(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $reply_id=Yii::$app->request->POST("reply_id");
        $user_id=Yii::$app->request->POST("user_id");
        if(!isset($reply_id)||!isset($user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        $StoryCommentReplyLikeLog_model = new StoryCommentReplyLikeLog();

        $result=$StoryCommentReplyLikeLog_model->apiLike($reply_id,$user_id);//数据库里去更新点赞数，存入缓存

        if ($result && Yii::$app->cache->exists('reply_id:'.$reply_id)){
            return parent::__response('ok',0,['likes'=>Yii::$app->cache->get('reply_id:'.$reply_id)]);
        }else{//缓存中都没有，初次访问然后去库中取
            $_response=array();
            $_response=self::__likes($reply_id);
            if (!empty($StoryCommentReplyLikeLog_model->error)){
                $_response['message']=$StoryCommentReplyLikeLog_model->error;
            }
            return $_response;
        }

    }

    /**
     *获取点赞评论条数
     */
    public function actionGetLikes(){
        $reply_id=Yii::$app->request->post('reply_id');
        if(!isset($reply_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        if (Yii::$app->cache->exists('reply_id:'.$reply_id)){
            $likes=Yii::$app->cache->get('reply_id:'.$reply_id);
            return parent::__response('ok',0,['reply_id'=>$reply_id,'likes'=>(int)$likes]);
        }else{
            return self::__likes($reply_id);
        }
    }


    //点赞数据库里提取，存入缓存中返回
    private static function __likes($reply_id){
        $content=StoryCommentReply::find()->where(['id'=>$reply_id])->select(['id','likes'])->asArray()->one();
        if (!$content){
            return [
                'message'=>'评论不存在',
                'code'=>(int)-1,
            ];
        }
        Yii::$app->cache->set('reply_id:'.$reply_id,(int)$content['likes']);
        return parent::__response('ok',0,['likes'=>(int)$content['likes']]);
    }

    
}
