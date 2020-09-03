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
     * 回复评论列表
     */
    public function actionReplyList(){

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
    public function actionReplyDetails(){

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
     * reply_type=1对评论发布回复 or reply_type=2对回复发布回复
     */
    public function actionAdd(){

        if(!Yii::$app->request->isPost){
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("comment_id")||!Yii::$app->request->POST("reply_type")||!Yii::$app->request->POST("reply_content")||!Yii::$app->request->POST("reply_from_uid")||!Yii::$app->request->POST("reply_to_uid")){
            return parent::__response('参数错误!',(int)-2);
        }
        $comment_id = (int)Yii::$app->request->post('comment_id');//评论id或者回复id  reply_type=1时是评论id，reply_type=2时是回复id
        $reply_type = (int)Yii::$app->request->post('reply_type');//1对评论发布回复 2对回复发布回复
        $reply_from_uid = (int)Yii::$app->request->post('reply_from_uid');//回复用户id
        $reply_to_uid = (int)Yii::$app->request->post('reply_to_uid');//回复目标用户id
        $reply_content = Yii::$app->request->post('reply_content');//回复内容

        if($reply_type==1){//对评论发布回复
            //先看评论是否存在
            $StoryCommen_Model=StoryComment::findOne($comment_id);
            if(!$StoryCommen_Model){
                return parent::__response('评论不存在!',(int)-1);
            }
        }elseif($reply_type==2){//对回复发布回复
            //先看回复的评论是否存在
            $StoryCommenReply_Model=StoryCommentReply::findOne($comment_id);
            if(!$StoryCommenReply_Model){
                return parent::__response('回复的评论不存在!',(int)-1);
            }
        }else{
            return parent::__response('reply_type参数值错误!',(int)-2);
        }

        if($reply_from_uid==$reply_to_uid){
            return parent::__response('不能对自己发表评论回复!',(int)-1);
        }

        $StoryCommentReply_model=new StoryCommentReply();
        $StoryCommentReply_model->comment_id = $comment_id;
        $StoryCommentReply_model->reply_type = $reply_type;
        $StoryCommentReply_model->reply_from_uid = $reply_from_uid;
        $StoryCommentReply_model->reply_to_uid = $reply_to_uid;
        $StoryCommentReply_model->reply_content = $reply_content;
        //$StoryCommentReply_model->status = 0;// 状态 0未读 1已读 2已回
        //$StoryCommentReply_model->reply_at=time();

        //验证保存
        $isValid = $StoryCommentReply_model->validate();
        if ($isValid) {
            $r=$StoryCommentReply_model->save();
            if($r){
                $reply_id=$StoryCommentReply_model->id;
                return parent::__response('回复评论成功',(int)0,['reply_id'=>$reply_id]);
            }else{
                return parent::__response('回复评论失败!',(int)-1);
            }
        }else{
            return parent::__response('参数错误!',(int)-2);
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
