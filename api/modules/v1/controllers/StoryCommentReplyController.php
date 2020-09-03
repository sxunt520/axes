<?php

namespace api\modules\v1\controllers;

use api\models\Member;
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
     * 评论的回复列表
     */
    public function actionReplyList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("comment_id")||!Yii::$app->request->POST("page")||!Yii::$app->request->POST("pagenum")){
            return parent::__response('参数错误!',(int)-2);
        }
        $comment_id = (int)Yii::$app->request->post('comment_id');//评论id
        //先看评论是否存在
        $StoryCommen_Model=StoryComment::findOne($comment_id);
        if(!$StoryCommen_Model){
            return parent::__response('评论不存在!',(int)-1);
        }
    	$page = (int)Yii::$app->request->post('page');//当前页
    	$pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
    	if ($page < 1) $page = 1;
    	if ($pagenum < 1) $pagenum = 5;
    	
         $StoryCommentReply_rows=StoryCommentReply::find()
            ->select(['id','comment_id','reply_type','reply_content','reply_from_uid','reply_to_uid','reply_at','status','is_show','likes'])
             ->andWhere(['=', 'comment_id', $comment_id])
             ->andWhere(['=', 'reply_type', 1])//reply_type=1时是，reply_type=2时是回复id
             ->andWhere(['=', 'is_show', 1])
            ->orderBy(['likes' => SORT_DESC,'id'=>SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$StoryCommentReply_rows){
            return parent::__response('获取失败',(int)-1);
        }

        return parent::__response('ok',0,$StoryCommentReply_rows);
        
    }

    /**
     * 评论的回复的详情
     */
    public function actionReplyDetails(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("reply_id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $reply_id = (int)Yii::$app->request->post('reply_id');

        $StoryCommentReply_row=StoryCommentReply::find()
            ->select(['reply_from_uid','reply_content','reply_at','likes'])
            ->andWhere(['=', 'id', $reply_id])
            ->andWhere(['=', 'reply_type', 1])
            ->asArray()
            ->one();

        if($StoryCommentReply_row){
            return parent::__response('ok',0,$StoryCommentReply_row);
        }else{
            return parent::__response('获取失败',(int)-1);
        }


    }

    /**
     * 评论的回复的详情_下面回复列表
     * comment_id page pagenum order_by
     */
    public function actionReplyDetailsList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("reply_id")||!Yii::$app->request->POST("page")||!Yii::$app->request->POST("pagenum")){
            return parent::__response('参数错误!',(int)-2);
        }
        $reply_id = (int)Yii::$app->request->post('reply_id');//回复评论id
        $order_by = (int)Yii::$app->request->post('order_by');//排序 1最新排序 2最早排序
        if(!isset($order_by)){ $order_by=1;}
        $order_by_arr=array();
        if($order_by==1){
            $order_by_arr=['id'=>SORT_DESC];
        }else{
            $order_by_arr=['id'=>SORT_ASC];
        }

        //先看回复评论是否存在
        $StoryCommentReply_Model=StoryCommentReply::findOne($reply_id);
        if(!$StoryCommentReply_Model){
            return parent::__response('回复评论不存在!',(int)-1);
        }
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 5;

        $StoryCommentReply_rows=StoryCommentReply::find()
            ->select(['id','comment_id','reply_type','reply_content','reply_from_uid','reply_to_uid','reply_at','status','is_show','likes'])
            ->andWhere(['=','comment_id',$reply_id])
            ->andWhere(['or','reply_type = 2','reply_type = 3'])//1对评论发布回复 2对回复发布回复 3@人+对回复发布回复 =2，3
            ->andWhere(['=', 'is_show', 1])
            ->orderBy($order_by_arr)
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$StoryCommentReply_rows){
            return parent::__response('获取失败',(int)-1);
        }

        return parent::__response('ok',0,$StoryCommentReply_rows);

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
        $comment_id = (int)Yii::$app->request->post('comment_id');//评论id或者回复id  reply_type=1时是目标评论id，reply_type=2时是目标回复id
        $reply_type = (int)Yii::$app->request->post('reply_type');//1对评论发布回复 2对回复发布回复 3@人+对回复发布回复
        $reply_from_uid = (int)Yii::$app->request->post('reply_from_uid');//回复用户id
        $reply_to_uid = (int)Yii::$app->request->post('reply_to_uid');//回复目标用户id @人就是@人的id，否则就是要回复的目标用户id
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
        }elseif($reply_type==3){//@人+对回复发布回复
            //先看回复的评论是否存在
            $StoryCommenReply_Model=StoryCommentReply::findOne($comment_id);
            if(!$StoryCommenReply_Model){
                return parent::__response('回复的评论不存在!',(int)-1);
            }
            //看看@的人是否存在
            $Member_Model=Member::findOne($reply_to_uid);
            if(!$Member_Model){
                return parent::__response('@的用户不存在!',(int)-1);
            }
        }else{
            return parent::__response('reply_type参数值错误!',(int)-2);
        }

//        if($reply_from_uid==$reply_to_uid){
//            return parent::__response('不能对自己发表评论回复!',(int)-1);
//        }

        $StoryCommentReply_model=new StoryCommentReply();
        $StoryCommentReply_model->comment_id = $comment_id;
        $StoryCommentReply_model->reply_type = $reply_type;
        $StoryCommentReply_model->reply_from_uid = $reply_from_uid;
        $StoryCommentReply_model->reply_to_uid = $reply_to_uid;
        $StoryCommentReply_model->reply_content = $reply_content;
        //$StoryCommentReply_model->is_show = 1;//是否显示
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
