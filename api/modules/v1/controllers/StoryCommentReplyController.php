<?php

namespace api\modules\v1\controllers;

use api\models\Member;
use api\models\SensitiveWords;
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
            ->select(['{{%story_comment_reply}}.*','{{%member}}.username','{{%member}}.nickname','{{%member}}.picture_url'])
             ->leftJoin('{{%member}}','{{%story_comment_reply}}.reply_from_uid={{%member}}.id')
             ->andWhere(['=', '{{%story_comment_reply}}.comment_id', $comment_id])
             ->andWhere(['=', '{{%story_comment_reply}}.reply_type', 1])//1对评论发布回复 2对回复发布回复 3@人+对回复发布回复
             ->andWhere(['=', '{{%story_comment_reply}}.is_show', 1])
            ->orderBy(['likes' => SORT_DESC,'id'=>SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();

        if(!$StoryCommentReply_rows){
            return parent::__response('暂无回复评论',(int)0,[]);
        }

         //回复数添加
        foreach($StoryCommentReply_rows as $k=>$v){
            $StoryCommentReply_rows[$k]['reply_num']=(int)StoryCommentReply::find()->andWhere(['comment_id'=>$v['comment_id'],'parent_reply_id'=>$v['id']])->andWhere(['in' , 'reply_type' , [2,3]])->count();

            //如果登录判断是否点赞
            if(!empty($this->Token)){
                $user_id = (int)Yii::$app->user->getId();//登录用户id
                $like_r=StoryCommentReplyLikeLog::find()->where(['reply_id' => $v['id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                    $StoryCommentReply_rows[$k]['is_like']=1;
                }else{
                    $StoryCommentReply_rows[$k]['is_like']=0;
                }
            }else{
                $StoryCommentReply_rows[$k]['is_like']=0;
            }

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

//        $StoryCommentReply_row=StoryCommentReply::find()
//            ->select(['reply_from_uid','reply_content','reply_at','likes'])
//            ->andWhere(['=', 'id', $reply_id])
//            ->andWhere(['=', 'reply_type', 1])
//            ->asArray()
//            ->one();
        $StoryCommentReply_row=StoryCommentReply::find()
            ->select(['{{%story_comment_reply}}.reply_from_uid','{{%story_comment_reply}}.reply_content','{{%story_comment_reply}}.reply_at','{{%story_comment_reply}}.likes','{{%member}}.username','{{%member}}.nickname','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%story_comment_reply}}.reply_from_uid={{%member}}.id')
            ->andWhere(['=', '{{%story_comment_reply}}.id', $reply_id])
            ->andWhere(['=', '{{%story_comment_reply}}.reply_type', 1])
            ->asArray()
            ->one();

        if($StoryCommentReply_row){

            //如果登录判断是否点赞
            if(!empty($this->Token)){
                $user_id = (int)Yii::$app->user->getId();//登录用户id
                $like_r=StoryCommentReplyLikeLog::find()->where(['reply_id' => $reply_id,'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                    $StoryCommentReply_row['is_like']=1;
                }else{
                    $StoryCommentReply_row['is_like']=0;
                }
            }else{
                $StoryCommentReply_row['is_like']=0;
            }

            return parent::__response('ok',0,$StoryCommentReply_row);
        }else{
            return parent::__response('暂无回复评论',(int)-0);
        }


    }

    /**
     * 评论的回复的详情_下面回复列表
     * reply_id page pagenum order_by
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

//        $StoryCommentReply_rows=StoryCommentReply::find()
//            ->select(['id','comment_id','reply_type','reply_content','reply_from_uid','reply_to_uid','reply_at','status','is_show','likes'])
//            ->andWhere(['=','comment_id',$reply_id])
//            ->andWhere(['or','reply_type = 2','reply_type = 3'])//1对评论发布回复 2对回复发布回复 3@人+对回复发布回复
//            ->andWhere(['=', 'is_show', 1])
//            ->orderBy($order_by_arr)
//            ->offset($pagenum * ($page - 1))
//            ->limit($pagenum)
//            ->asArray()
//            ->all();

        $StoryCommentReply_rows=StoryCommentReply::find()
            ->select(['{{%story_comment_reply}}.*','{{%member}}.username','{{%member}}.nickname','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%story_comment_reply}}.reply_from_uid={{%member}}.id')
            ->andWhere(['=', '{{%story_comment_reply}}.parent_reply_id', $reply_id])
            ->andWhere(['or','{{%story_comment_reply}}.reply_type = 2','{{%story_comment_reply}}.reply_type = 3'])//1对评论发布回复 2对回复发布回复 3@人+对回复发布回复
            ->andWhere(['=', '{{%story_comment_reply}}.is_show', 1])
            ->orderBy($order_by_arr)
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();

        if(!$StoryCommentReply_rows){
            return parent::__response('暂无回复评论',(int)0);
        }

        //回复数添加
        foreach($StoryCommentReply_rows as $k=>$v){

            //如果登录判断是否点赞
            if(!empty($this->Token)){
                $user_id = (int)Yii::$app->user->getId();//登录用户id
                $like_r=StoryCommentReplyLikeLog::find()->where(['reply_id' => $v['id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                    $StoryCommentReply_rows[$k]['is_like']=1;
                }else{
                    $StoryCommentReply_rows[$k]['is_like']=0;
                }
            }else{
                $StoryCommentReply_rows[$k]['is_like']=0;
            }

        }

        return parent::__response('ok',0,$StoryCommentReply_rows);

    }

    /**
     * @params
     * comment_id 评论id
     * reply_type=1 对评论发布回复 2对回复发布回复 3@人+对回复发布回复
     * reply_to_uid 回复目标用户id @人就是@人的id，否则就是要回复的目标用户id
     * reply_content 回复内容
     * reply_id 当reply_type=2,3 时 需要传入参数回复reply_id @人也是传入回复详情的id
     */
    public function actionAdd(){

        if(!Yii::$app->request->isPost){
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("comment_id")||!Yii::$app->request->POST("reply_type")||!Yii::$app->request->POST("reply_content")||!Yii::$app->request->POST("reply_to_uid")){
            return parent::__response('参数错误!',(int)-2);
        }
        $comment_id = (int)Yii::$app->request->post('comment_id');//评论comment_id
        $reply_type = (int)Yii::$app->request->post('reply_type');//1对评论发布回复 2对回复发布回复 3@人+对回复发布回复
        $reply_from_uid = (int)Yii::$app->user->getId();//回复用户id
        $reply_to_uid = (int)Yii::$app->request->post('reply_to_uid');//回复目标用户id @人就是@人的id，否则就是要回复的目标用户id
        $reply_content = Yii::$app->request->post('reply_content');//回复内容
        $reply_id = (int)Yii::$app->request->post('reply_id');//父回复id
        if($reply_id>0){//如果传入回复id 父回复id就是reply_id， 否刚是comment_id
            $parent_reply_id=$reply_id;
        }else{
            $parent_reply_id=$comment_id;
        }

        //敏感关键词过滤
        $SensitiveWords_r=SensitiveWords::matching_sensitive_one2($reply_content);//匹配结果
        if($SensitiveWords_r['is_sensitive']==true){
            return parent::__response('回复失败!含敏感词{'.$SensitiveWords_r['sensitive_str'].'}',(int)-2);
        }

        if($reply_type==1){//对评论发布回复
            //先看评论是否存在
            $StoryCommen_Model=StoryComment::findOne($comment_id);
            if(!$StoryCommen_Model){
                return parent::__response('评论不存在!',(int)-1);
            }
        }elseif($reply_type==2&&$reply_id>0){//对回复发布回复
            //先看回复的评论是否存在
            $StoryCommenReply_Model=StoryCommentReply::findOne($reply_id);
            if(!$StoryCommenReply_Model){
                return parent::__response('回复的评论不存在!',(int)-1);
            }
        }elseif($reply_type==3&&$reply_id>0){//@人+对回复发布回复
            //先看回复的评论是否存在
            $StoryCommenReply_Model=StoryCommentReply::findOne($reply_id);
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
        $StoryCommentReply_model->parent_reply_id=$parent_reply_id;//父回复id
        //$StoryCommentReply_model->is_show = 1;//是否显示
        //$StoryCommentReply_model->status = 0;// 状态 0未读 1已读 2已回
        //$StoryCommentReply_model->reply_at=time();

        //验证保存
        $isValid = $StoryCommentReply_model->validate();
        if ($isValid) {
            $r=$StoryCommentReply_model->save();
            if($r){

                ////锁定行 更新+100热度
                $sql="select likes from {{%story_comment}} where id={$comment_id} for update";
                $data=Yii::$app->db->createCommand($sql)->query()->read();////锁定行
                $sql="update {{%story_comment}} set heart_val=heart_val+100 where id={$comment_id}";
                Yii::$app->db->createCommand($sql)->execute();

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
        $user_id=Yii::$app->user->getId();
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
                $_response['status']=(int)-1;
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
                'status'=>(int)-1,
            ];
        }
        Yii::$app->cache->set('reply_id:'.$reply_id,(int)$content['likes']);
        return parent::__response('ok',0,['likes'=>(int)$content['likes']]);
    }

    
}
