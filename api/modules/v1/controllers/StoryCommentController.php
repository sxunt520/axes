<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use api\models\Story;
use api\models\StoryTag;
use api\models\StoryComment;
use api\models\StoryCommentImg;
use api\models\StoryCommentLikeLog;
use api\models\member;

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
        if(!$StoryComment_rows){
            return parent::__response('获取失败',(int)-1);
        }
         
      //图片、用户头像名字、标签
        foreach ($StoryComment_rows as $k=>$v){
            $StoryCommentImg=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $v['comment_img_id']])->asArray()->one();
            if($StoryCommentImg)$StoryComment_rows[$k]['comment_img']=$StoryCommentImg['img_url'];

            $member_arr=Member::find()->select(['username','picture_url'])->where(['id' => $v['from_uid']])->asArray()->one();
            if($member_arr){
                $StoryComment_rows[$k]['user_name']=$member_arr['username'];
                $StoryComment_rows[$k]['user_picture']=$member_arr['picture_url'];
            }else{
                $StoryComment_rows[$k]['user_name']='';
                $StoryComment_rows[$k]['user_picture']='';
            }

            $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['story_id']])->asArray()->all();
            if($StoryTag_rows) $StoryComment_rows[$k]['tags']=$StoryTag_rows;

        }

        return parent::__response('ok',0,$StoryComment_rows);
        
    }

    /**
     * 故事评论列表页 首页点进去看的评论列表页   order_by 排序选择 最新、最热
     * @param story_id page pagenum  order_by
     */
    public function actionList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("page")||!Yii::$app->request->POST("pagenum")){
            return parent::__response('参数错误!',(int)-2);
        }
        $story_id = (int)Yii::$app->request->post('story_id');//故事id
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 5;

        //先看故事是否存在
        $Story_Model=Story::findOne($story_id);
        if(!$Story_Model){
            return parent::__response('故事不存在!',(int)-1);
        }

        $data=array();
        ///////////获取故事详细内容及标签人气值////////
        $Story_row=Story::find()
            ->select(['title','next_updated_at','likes','views','share_num'])
            ->andWhere(['=', 'id', $story_id])
            ->asArray()
            ->one();
        //故事评论数
        $story_comment_num=StoryComment::find()->where(['story_id'=>$story_id])->count();
        if(!$story_comment_num){$story_comment_num=0;}
        //故事人气值	计算规则：人气值外显代表 游戏观看数+评论+转发的虚拟数值总和  1次观看=10点人气，一条评论=50点人气，一次转发=100点人气
        //$data['story_details']
        $data['story_details']['title']=$Story_row['title'];
        $data['story_details']['next_updated_at']=$Story_row['next_updated_at'];
        $data['story_details']['popular_val']=$Story_row['views']*10+$story_comment_num*50+$Story_row['share_num']*100;//人气值
        //标签
        $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $story_id])->asArray()->all();
        if($StoryTag_rows){
            $data['story_details']['story_tag']=$StoryTag_rows;
        }else{
            $data['story_details']['story_tag']=[];
        }

        $order_by = (int)Yii::$app->request->post('order_by');//排序 1最新、2最热
        if(!isset($order_by)){ $order_by=1;}
        $order_by_arr=array();
        if($order_by==2){
            $order_by_arr=['heart_val'=>SORT_DESC,'id'=>SORT_DESC];//热度值
        }else{
            $order_by_arr=['id'=>SORT_DESC];
        }

        $StoryComment_rows=StoryComment::find()
            ->select(['id','story_id','title','content','from_uid','created_at','comment_img_id','heart_val','is_plot','likes'])
            ->andWhere(['=', 'story_id', $story_id])
            ->andWhere(['=', 'is_show', 1])
            ->orderBy($order_by_arr)
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$StoryComment_rows){
            return parent::__response('获取失败',(int)-1);
        }

        //图片
        foreach ($StoryComment_rows as $k=>$v){

            $StoryCommentImg=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $v['comment_img_id']])->asArray()->one();
            if($StoryCommentImg)$StoryComment_rows[$k]['comment_img']=$StoryCommentImg['img_url'];

            $member_arr=Member::find()->select(['username','picture_url'])->where(['id' => $v['from_uid']])->asArray()->one();
            if($member_arr){
                $StoryComment_rows[$k]['user_name']=$member_arr['username'];
                $StoryComment_rows[$k]['user_picture']=$member_arr['picture_url'];
            }else{
                $StoryComment_rows[$k]['user_name']='';
                $StoryComment_rows[$k]['user_picture']='';
            }

//            $StoryCommentImg=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $v['comment_img_id']])->asArray()->one();
//            if($StoryCommentImg)$StoryComment_rows[$k]['comment_img']=$StoryCommentImg;
        }

        $data['story_comment_list']=$StoryComment_rows;

        return parent::__response('ok',0,$data);

    }

    /**
     * 评论详情页内容 下面回复列表见回复控制器
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

        //先看评论是否存在
        if(!$StoryComment_row){
            return parent::__response('评论不存在!',(int)-1);
        }

        // 浏览量变化
        StoryComment::addView($id);//缓存添加操作
        $StoryComment_row['views']=$StoryComment_row['views']+\Yii::$app->cache->get('story_comment:views:' . $id);//获取真实的浏览量 StoryComment::getTrueViews($id);

        $member_arr=Member::find()->select(['username','picture_url'])->where(['id' => $StoryComment_row['from_uid']])->asArray()->one();
        if($member_arr){
            $StoryComment_row['user_name']=$member_arr['username'];
            $StoryComment_row['user_picture']=$member_arr['picture_url'];
        }else{
            $StoryComment_row['user_name']='';
            $StoryComment_row['user_picture']='';
        }

        //标签
        //$StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $id])->asArray()->all();
        //if($StoryTag_rows) $StoryComment_row['tags']=$StoryTag_rows;

        //评论图
        if($StoryComment_row['comment_img_id']>0){
            $StoryCommentImg_rows=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $StoryComment_row['comment_img_id']])->asArray()->all();
            if($StoryCommentImg_rows) $StoryComment_row['comment_img']=$StoryCommentImg_rows;
        }

        //多少人赞过 仅显示最开始点赞的6位用户
        $sql_num="select count(*) from (select * from {{%story_comment_like_log}} where comment_id={$id} group by user_id) as like_log_num;";
        $likes_num=(int)Yii::$app->db->createCommand($sql_num)->queryScalar();
        if($likes_num){//多少人赞
            $StoryComment_row['likes_num']=$likes_num;
        }else{
            $StoryComment_row['likes_num']=0;
        }
        //赞的人信息
        $sql_arr="select like_log.comment_id,like_log.user_id,like_log.create_at,s_member.username,s_member.picture_url from (select * from {{%story_comment_like_log}} where comment_id={$id} group by user_id) as like_log INNER JOIN {{%member}} on like_log.user_id=s_member.id ORDER BY like_log.create_at ASC limit 6";
        $likes_arr=Yii::$app->db->createCommand($sql_arr)->queryAll();
        if($likes_arr){
            $StoryComment_row['likes_user_arr']=$likes_arr;
        }else{
            $StoryComment_row['likes_user_arr']=[];
        }


        //回复的评论 这里见回回复控制器接口

        return parent::__response('ok',0,$StoryComment_row);

    }

    /**
     * 发布评论
     */
    public function actionAdd(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("comment_img_id")||!Yii::$app->request->POST("title")||!Yii::$app->request->POST("content")){
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
        $story_comment_model->from_uid = Yii::$app->user->getId();//评论用户id
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
     *评论点赞
     */
    public function actionLike(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $comment_id=Yii::$app->request->POST("comment_id");
        $user_id=Yii::$app->user->getId();
        if(!isset($comment_id)||!isset($user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        $StoryCommentLikeLog_model = new StoryCommentLikeLog();

        $result=$StoryCommentLikeLog_model->apiLike($comment_id,$user_id);//数据库里去更新点赞数、加热度，存入缓存

        if ($result && Yii::$app->cache->exists('comment_id:'.$comment_id)){
            return parent::__response('ok',0,['likes'=>Yii::$app->cache->get('comment_id:'.$comment_id)]);
        }else{//缓存中都没有，初次访问然后去库中取
            $_response=array();
            $_response=self::__likes($comment_id);
            if (!empty($StoryCommentLikeLog_model->error)){
                $_response['message']=$StoryCommentLikeLog_model->error;
                $_response['status']=(int)-1;
            }
            return $_response;
        }

    }

    /**
     *获取点赞评论条数
     */
    public function actionGetLikes(){
        $comment_id=Yii::$app->request->post('comment_id');
        if(!isset($comment_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        if (Yii::$app->cache->exists('comment_id:'.$comment_id)){
            $likes=Yii::$app->cache->get('comment_id:'.$comment_id);
            return parent::__response('ok',0,['comment_id'=>$comment_id,'likes'=>(int)$likes]);
        }else{
            return self::__likes($comment_id);
        }
    }


    //点赞数据库里提取，存入缓存中返回
    private static function __likes($comment_id){
        $content=StoryComment::find()->where(['id'=>$comment_id])->select(['id','likes'])->asArray()->one();
        if (!$content){
            return [
                'message'=>'评论不存在',
                'status'=>(int)-1,
            ];
        }
        Yii::$app->cache->set('comment_id:'.$comment_id,(int)$content['likes']);
        return parent::__response('ok',0,['likes'=>(int)$content['likes']]);
    }

    
}