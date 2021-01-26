<?php

namespace api\modules\v1\controllers;

use api\models\Follower;
use Yii;
use api\components\BaseController;
use api\models\Story;
use api\models\StoryTag;
use api\models\StoryComment;
use api\models\StoryCommentImg;
use api\models\StoryCommentLikeLog;
use api\models\member;
use api\models\StoryScreenComment;
use api\models\SensitiveWords;

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

        if(!is_array($StoryComment_rows)){
            return parent::__response('暂无数据',(int)0);
        }
         
      //图片、用户头像名字、标签
        foreach ($StoryComment_rows as $k=>$v){

            $StoryCommentImg=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $v['comment_img_id']])->asArray()->one();
            if($StoryCommentImg){
                $StoryComment_rows[$k]['comment_img']=$StoryCommentImg['img_url'];
            }else{
                $StoryComment_rows[$k]['comment_img']='';
            }

            $member_arr=Member::find()->select(['username','nickname','picture_url'])->where(['id' => $v['from_uid']])->asArray()->one();
            if($member_arr){
                $StoryComment_rows[$k]['user_name']=$member_arr['username'];
                $StoryComment_rows[$k]['nickname']=$member_arr['nickname'];
                $StoryComment_rows[$k]['user_picture']=$member_arr['picture_url'];
            }else{
                $StoryComment_rows[$k]['user_name']='';
                $StoryComment_rows[$k]['nickname']='';
                $StoryComment_rows[$k]['user_picture']='';
            }


            //如果登录判断是否点赞
            if(!empty($this->Token)){
                $user_id = (int)Yii::$app->user->getId();//登录用户id
                $like_r=StoryCommentLikeLog::find()->where(['comment_id' => $v['id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                    $StoryComment_rows[$k]['is_like']=1;
                }else{
                    $StoryComment_rows[$k]['is_like']=0;
                }
            }else{
                $StoryComment_rows[$k]['is_like']=0;
            }


            //游戏名
            $game_title=Story::find()->select(['game_title'])->where(['id'=>$v['story_id']])->scalar();
            if($game_title){
                $StoryComment_rows[$k]['game_title']=$game_title;
            }else{
                $StoryComment_rows[$k]['game_title']='';
            }

            $StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $v['story_id']])->asArray()->all();
            if($StoryTag_rows){
                $StoryComment_rows[$k]['tags']=$StoryTag_rows;
            }else{
                $StoryComment_rows[$k]['tags']=[];
            }

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
            ->select(['title','next_updated_at','likes','views','share_num','game_title'])
            ->andWhere(['=', 'id', $story_id])
            ->asArray()
            ->one();
        //故事评论数
        $story_comment_num=StoryComment::find()->where(['story_id'=>$story_id])->count();
        if(!$story_comment_num){$story_comment_num=0;}
        //故事人气值	计算规则：人气值外显代表 游戏观看数+评论+转发的虚拟数值总和  1次观看=10点人气，一条评论=50点人气，一次转发=100点人气
        //$data['story_details']
        $data['story_details']['title']=$Story_row['title'];
        $data['story_details']['game_title']=$Story_row['game_title'];
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
            //return parent::__response('获取失败',(int)-1);
            $StoryComment_rows=[];
        }

        //图片
        foreach ($StoryComment_rows as $k=>$v){

            $StoryCommentImg=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $v['comment_img_id']])->asArray()->one();
            if($StoryCommentImg)$StoryComment_rows[$k]['comment_img']=$StoryCommentImg['img_url'];

            $member_arr=Member::find()->select(['username','nickname','picture_url'])->where(['id' => $v['from_uid']])->asArray()->one();
            if($member_arr){
                $StoryComment_rows[$k]['user_name']=$member_arr['username'];
                $StoryComment_rows[$k]['nickname']=$member_arr['nickname'];
                $StoryComment_rows[$k]['user_picture']=$member_arr['picture_url'];
            }else{
                $StoryComment_rows[$k]['user_name']='';
                $StoryComment_rows[$k]['nickname']='';
                $StoryComment_rows[$k]['user_picture']='';
            }

            //如果登录判断是否点赞
            if(!empty($this->Token)){
                $user_id = (int)Yii::$app->user->getId();//登录用户id
                $like_r=StoryCommentLikeLog::find()->where(['comment_id' => $v['id'],'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
                if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                    $StoryComment_rows[$k]['is_like']=1;
                }else{
                    $StoryComment_rows[$k]['is_like']=0;
                }
            }else{
                $StoryComment_rows[$k]['is_like']=0;
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

        $member_arr=Member::find()->select(['nickname','username','picture_url'])->where(['id' => $StoryComment_row['from_uid']])->asArray()->one();
        if($member_arr){
            $StoryComment_row['user_name']=$member_arr['username'];
            $StoryComment_row['nickname']=$member_arr['nickname'];
            $StoryComment_row['user_picture']=$member_arr['picture_url'];
        }else{
            $StoryComment_row['user_name']='';
            $StoryComment_row['nickname']='';
            $StoryComment_row['user_picture']='';
        }

        //如果用户登录的，显示关注相关的状态 关注类型 0无状态 1关注 2拉黑，是否已点赞
        if(!empty($this->Token)){
            $user_id = (int)Yii::$app->user->getId();//登录用户id
            //echo $user_id.'-----'.$StoryComment_row['from_uid'];exit;
            $follower_model=Follower::find()->where(['from_user_id'=>$user_id,'to_user_id'=>$StoryComment_row['from_uid'],'follower_type'=>1])->one();//看一下登录用户有没有关注他
            if($follower_model){
                $StoryComment_row['follower_text']='已关注';
                $StoryComment_row['follower_type']=1;
            }else{
                $StoryComment_row['follower_text']='未关注';
                $StoryComment_row['follower_type']=0;
            }

            //点赞状况
            $like_r=StoryCommentLikeLog::find()->where(['comment_id' => $id,'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
            if ($like_r && time()-($like_r->create_at) < Yii::$app->params['user.liketime']){
                $StoryComment_row['is_like']=1;
            }else{
                $StoryComment_row['is_like']=0;
            }
        }else{
            $StoryComment_row['follower_text']='未关注';
            $StoryComment_row['follower_type']=0;
            $StoryComment_row['is_like']=0;
        }

        //标签
        //$StoryTag_rows=StoryTag::find()->select(['id','tag_name'])->where(['story_id' => $id])->asArray()->all();
        //if($StoryTag_rows) $StoryComment_row['tags']=$StoryTag_rows;

        //游戏名
        $game_title=Story::find()->select(['game_title'])->where(['id'=>$StoryComment_row['story_id']])->scalar();
        if($game_title){
            $StoryComment_row['game_title']=$game_title;
        }else{
            $StoryComment_row['game_title']='';
        }

        //评论图
        if($StoryComment_row['comment_img_id']>0){
            $StoryCommentImg_rows=StoryCommentImg::find()->select(['id','img_url','img_text'])->where(['id' => $StoryComment_row['comment_img_id']])->asArray()->one();
            if($StoryCommentImg_rows)$StoryComment_row['comment_img']=$StoryCommentImg_rows['img_url'];
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
        $sql_arr="select like_log.comment_id,like_log.user_id,like_log.create_at,s_member.username,s_member.nickname,s_member.picture_url from (select * from {{%story_comment_like_log}} where comment_id={$id} group by user_id) as like_log INNER JOIN {{%member}} on like_log.user_id=s_member.id ORDER BY like_log.create_at ASC limit 6";
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
        if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("comment_img_id")||!Yii::$app->request->POST("title")){
            return parent::__response('参数错误!',(int)-2);
        }
        $story_id = (int)Yii::$app->request->post('story_id');//故事id
        $comment_img_id = (int)Yii::$app->request->post('comment_img_id');//评论图id
        $title=Yii::$app->request->post('title');//标题
        $content=Yii::$app->request->post('content');//内容

        //敏感关键词过滤
        $sensitive_str=$title.$content;//过滤内容字符串
        $SensitiveWords_r=SensitiveWords::matching_sensitive_one2($sensitive_str);//匹配结果
        if($SensitiveWords_r['is_sensitive']==true){
            return parent::__response('评论失败!含敏感词{'.$SensitiveWords_r['sensitive_str'].'}',(int)-2);
        }


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
        $story_comment_model->title = $title;//标题
        $story_comment_model->content = $content;//内容
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
     *Time:2020/10/28 10:13
     *Author:始渲
     *Remark:用户更新评论
     * @params: comment_id  comment_img_id  title  content  is_plot
     *
     */
    public function actionEdit(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("comment_id")||!Yii::$app->request->POST("comment_img_id")||!Yii::$app->request->POST("title")){
            return parent::__response('参数错误!',(int)-2);
        }
        $comment_id = (int)Yii::$app->request->post('comment_id');//评论id
        $comment_img_id = (int)Yii::$app->request->post('comment_img_id');//评论图id
        $from_uid=Yii::$app->user->getId();//登录用户id


        //先看评论是否存在
        $StoryComment_Model=StoryComment::findOne($comment_id);
        if(!$StoryComment_Model){
            return parent::__response('评论不存在!',(int)-1);
        }

        //判断评论是否是登录用户所评论
        if($StoryComment_Model->from_uid!=$from_uid){
            return parent::__response('更新失败，此评论不是此登录用户所评论!',(int)-1);
        }

        //看选择的图片comment_img_id是否故事下面的图片
        if($StoryComment_Model->story_id > 0){
            $StoryCommentImg=StoryCommentImg::findOne($comment_img_id);
            if(!$StoryCommentImg||(int)$StoryCommentImg->story_id!=$StoryComment_Model->story_id){
                return parent::__response('所传入的图片参数图片不存在，或者参数不是故事所属图片组!',(int)-1);
            }
        }else{
            return parent::__response('评论所属游戏不存在',(int)-1);
        }

        $StoryComment_Model->comment_img_id = $comment_img_id;//评论图id
        $StoryComment_Model->title = Yii::$app->request->post('title');//标题
        $StoryComment_Model->content = Yii::$app->request->post('content');//内容
        $StoryComment_Model->is_plot = Yii::$app->request->post('is_plot');//是否包含剧透 1是 0否
        $StoryComment_Model->from_uid = $from_uid;//评论用户id
        //$StoryComment_Model->created_at=time();
        $StoryComment_Model->update_at=time();

        //验证保存
        $isValid = $StoryComment_Model->validate();
        if ($isValid) {
            $r=$StoryComment_Model->save();
            if($r){
                $comment_id=$StoryComment_Model->id;
                return parent::__response('修改评论成功',(int)0,['comment_id'=>$comment_id,'update_at'=>$StoryComment_Model->update_at]);
            }else{
                return parent::__response('修改评论失败!',(int)-1);
            }
        }else{
            return parent::__response('参数错误!',(int)-2);
        }


    }

    /**
     *Time:2020/10/28 10:13
     *Author:始渲
     *Remark:用户删除隐藏评论
     * @params: comment_id
     *
     */
    public function actionDel(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("comment_id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $comment_id = (int)Yii::$app->request->post('comment_id');//评论id
        $from_uid=Yii::$app->user->getId();//登录用户id

        //先看评论是否存在
        $StoryComment_Model=StoryComment::findOne($comment_id);
        if(!$StoryComment_Model){
            return parent::__response('评论不存在!',(int)-1);
        }

        //判断评论是否是登录用户所评论
        if($StoryComment_Model->from_uid!=$from_uid){
            return parent::__response('删除失败，此评论不是此登录用户所评论!',(int)-1);
        }

        //判断评论是否已经隐藏删除
        if($StoryComment_Model->is_show==0){
            return parent::__response('操作失败，此评论已经删除!',(int)-1);
        }

        $StoryComment_Model->is_show = 0;//是否隐藏删除 1是 0否
        $StoryComment_Model->from_uid = $from_uid;//评论用户id
        $StoryComment_Model->update_at=time();

        //验证保存
        $isValid = $StoryComment_Model->validate();
        if ($isValid) {
            $r=$StoryComment_Model->save();
            if($r){
                $comment_id=$StoryComment_Model->id;
                return parent::__response('删除评论成功',(int)0,['comment_id'=>$comment_id]);
            }else{
                return parent::__response('删除评论失败!',(int)-1);
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
            return parent::__response('后台没添加评论图片组数据', 0,[]);
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


    /**
     *Time:2020/11/18 9:48
     *Author:始渲
     *Remark:更新评论分享数 share_num
     * @params:comment_id
     *
     */
    public function actionShare(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("comment_id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $comment_id=Yii::$app->request->POST("comment_id");
        //$user_id=Yii::$app->user->getId();

        //先看故事是否存在
        $StoryComment=StoryComment::findOne($comment_id);
        if(!$StoryComment){
            return parent::__response('评论不存在!',(int)-1);
        }

        //锁定行,更新
        $sql="select share_num from {{%story_comment}} where id={$comment_id} for update";
        $data=Yii::$app->db->createCommand($sql)->query()->read();
        $sql="update {{%story_comment}} set share_num=share_num+1 where id={$comment_id}";
        $r=Yii::$app->db->createCommand($sql)->execute();
        if($r){
            return parent::__response('分享成功',(int)0,['share_num'=>$data['share_num']+1]);
        }else{
            return parent::__response('分享失败',(int)-1);
        }

    }


    /**
     *Time:2020/12/11 13:48
     *Author:始渲
     *Remark:添加弹幕
     * @params:
     * story_id 故事游戏id
     * text 弹幕文字
     */
    public function actionAddScreenComment(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("story_id")||!Yii::$app->request->POST("text")){
            return parent::__response('参数错误!',(int)-2);
        }
        $story_id = (int)Yii::$app->request->post('story_id');//故事游戏id
        $text = Yii::$app->request->post('text');//弹幕文字

        //敏感关键词过滤
        $SensitiveWords_r=SensitiveWords::matching_sensitive_one2($text);//匹配结果
        if($SensitiveWords_r['is_sensitive']==true){
            return parent::__response('发送弹幕失败!含敏感词{'.$SensitiveWords_r['sensitive_str'].'}',(int)-2);
        }

        //先看故事是否存在
        $Story_Model=Story::findOne($story_id);
        if(!$Story_Model){
            return parent::__response('故事游戏不存在!',(int)-1);
        }

        $StoryScreenComment_model=new StoryScreenComment();
        $StoryScreenComment_model->story_id = $story_id;//故事id
        $StoryScreenComment_model->text = $text;//评论图id
        $StoryScreenComment_model->from_uid = Yii::$app->user->getId();//评论用户id
        $StoryScreenComment_model->created_at=time();
        $StoryScreenComment_model->is_show=1;

        //验证保存
        $isValid = $StoryScreenComment_model->validate();
        if ($isValid) {
            $r=$StoryScreenComment_model->save();
            if($r){
                $screen_comment_id=$StoryScreenComment_model->id;
                return parent::__response('添加弹幕成功',(int)0,['screen_comment_id'=>$screen_comment_id]);
            }else{
                return parent::__response('添加弹幕失败!',(int)-1);
            }
        }else{
            return parent::__response('参数错误!',(int)-2);
        }

    }

    /**
     *Time:2020/12/11 14:10
     *Author:始渲
     *Remark:故事游戏_弹幕列表
     * @params:
     * story_id 故事游戏id
     * page 当前页
     * pagenum 一页显示多少
     */
    public function actionScreenCommentList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        if(!Yii::$app->request->POST("story_id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $story_id = (int)Yii::$app->request->post('story_id');//故事游戏id

        //先看故事是否存在
        $Story_Model=Story::findOne($story_id);
        if(!$Story_Model){
            return parent::__response('故事游戏不存在!',(int)-1);
        }

        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 10;

        $StoryScreenComment_rows=StoryScreenComment::find()
            //->select(['id','title','img_url','order_by'])
            ->andWhere(['=', 'story_id', $story_id])
            ->andWhere(['=', 'is_show', 1])
            ->orderBy(['id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if($StoryScreenComment_rows){
            return parent::__response('ok',0,$StoryScreenComment_rows);
        }else{
            return parent::__response('暂无数据',0);
        }

    }
    
}