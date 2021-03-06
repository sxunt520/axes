<?php

namespace api\modules\v1\controllers;

use api\models\Report;
use api\models\Shield;
use api\models\Story;
use api\models\StoryCommentLikeLog;
use api\models\StoryCommentReplyLikeLog;
use Yii;
use api\components\BaseController;
use yii\web\IdentityInterface;
use api\models\Member;
use api\models\LoginForm;
use api\models\MobileLoginForm;
use api\models\StoryComment;
use api\models\StoryCommentImg;
use api\models\StoryCommentReply;
use api\models\StoryAnnouncePushLog;
use api\models\Follower;
use api\models\UploadForm;
use yii\web\UploadedFile;
use api\models\TravelRecord;
use api\models\SmsLog;
use api\models\MemberAuths;
use api\models\ThirdLoginForm;
use api\components\SendSms;

class MemberController extends BaseController
{
    public $modelClass = 'api\models\Member';

	/**
	 * 添加测试用户
	 */
	public function actionSignup ()
	{
	    $user = new Member();
	    $user->username = 'waacking';
	    $user->setPassword('waacking');
	    $user->generateAuthKey();
	    $user->save(false);
	    return [ 'code' => 200 ];

	    //插入成功后会自向profile表里插其它的数据 common下 afterInsertInternal
        //return parent::__response('待续开发',-1);
	 }

	 /**
        * 登录
    */
	public function actionLogin ()
    {
        $model = new LoginForm;
        $model->setAttributes(Yii::$app->request->post());
        if ($user = $model->login()) {
            if ($user instanceof IdentityInterface) {
                //return $user->api_token;
                return parent::__response('ok',0,['Token'=>$user->api_token]);
            } else {
                return $user->errors;
            }
        } else {
            return $model->errors;
            //var_dump($user->errors);
            //return parent::__response($user->errors,-1);
        }
	}
	
	/**
	 * 获取用户信息
	 */
	public function actionUserProfile ()
	{
	    // 到这一步，token都认为是有效的了
	    // 下面只需要实现业务逻辑即可，下面仅仅作为案例，比如你可能需要关联其他表获取用户信息等等
	    $headers = Yii::$app->getRequest()->getHeaders();
	    $token = $headers->get('token');
	    $user = Member::findIdentityByAccessToken($token);
	    if(!$user){
            return parent::__response('Token失效错误',-1);
        }

	    $user_profile=[
	        'id'=>$user->id,
            'username'=>!empty($user->username)?$user->username:'',
            'picture_url'=>!empty($user->picture_url)?$user->picture_url:'',
            'nickname'=>!empty($user->nickname)?$user->nickname:'',
            'signature'=>!empty($user->signature)?$user->signature:'',
            'real_name_status'=>!empty($user->real_name_status)?$user->real_name_status:0,
            'mobile'=>!empty($user->mobile)?$user->mobile:'',
        ];

       // var_dump(Yii::$app->user->getId());exit;// 获取用户id
        return parent::__response('ok',0,$user_profile);
	}

    /**
     * 个人中心首页
     */
    public function actionMy()
    {
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        $user_id=Yii::$app->request->POST("user_id");
        $data=array();

        //先看是否有传user_id,然后看目标用户的个人中心信息
        if(isset($user_id)&&$user_id>0){

            //用户个人基本信息
            $user_profile=Member::find()->select(['id','username','picture_url','nickname','signature','real_name_status','mobile'])->where(['id'=>$user_id])->asArray()->one();
            if(!$user_profile){
                return parent::__response('用户不存在',-1);
            }else{
                if($user_profile['signature']==NULL){
                    $user_profile['signature']='';
                }
                $data['user_profile']=$user_profile;
            }


            //如果用户登录的，显示关注相关的状态 关注类型 0无状态 1关注 2拉黑
            if(!empty($this->Token)){
                $login_user_id = (int)Yii::$app->user->getId();//登录用户id
                //echo $user_id.'-----'.$StoryComment_row['from_uid'];exit;
                $follower_model=Follower::find()->where(['from_user_id'=>$login_user_id,'to_user_id'=>$user_id,'follower_type'=>1])->one();//看一下登录用户有没有关注他
                if($follower_model){
                    $data['user_profile']['follower_text']='已关注';
                    $data['user_profile']['follower_type']=1;
                }else{
                    $data['user_profile']['follower_text']='未关注';
                    $data['user_profile']['follower_type']=0;
                }
            }else{
                $data['user_profile']['follower_text']='未关注';
                $data['user_profile']['follower_type']=0;
            }

            //关注数
            $data['follower_count']=(int)Follower::find()->where(['from_user_id'=>$user_id,'follower_type'=>1])->count();
            //热度值
            $data['heart_val_count']=(int)StoryComment::find()->select('sum(heart_val) as heart_val_count')->where(['from_uid'=>$user_id,'is_show'=>1])->groupBy('from_uid')->asArray()->scalar();
            //粉丝数
            $data['fans_count']=(int)Follower::find()->where(['to_user_id'=>$user_id,'follower_type'=>1])->count();

            //Ta的评论
            $mylast_comment_row=StoryComment::find()
                ->select(['{{%story_comment}}.id','{{%story_comment}}.title','{{%story_comment_img}}.img_url'])
                ->leftJoin('{{%story_comment_img}}','{{%story_comment}}.comment_img_id={{%story_comment_img}}.id')
                ->andWhere(['=', '{{%story_comment}}.from_uid', $user_id])
                ->andWhere(['=', '{{%story_comment}}.is_show', 1])
                ->orderBy(['{{%story_comment}}.id'=>SORT_DESC])
                ->asArray()
                ->one();
            if($mylast_comment_row){
                $data['mylast_comment_row']=$mylast_comment_row;
            }else{
                $data['mylast_comment_row']=[];
            }

            //Ta的旅行记录
            $travel_record=TravelRecord::find()
                ->select(['{{%travel_record}}.story_id','{{%travel_record}}.history_chapters','{{%story}}.title','{{%story}}.record_pic'])
                ->leftJoin('{{%story}}','{{%travel_record}}.story_id={{%story}}.id')
                ->andWhere(['=', '{{%travel_record}}.user_id', $user_id])
                ->orderBy(['{{%travel_record}}.id'=>SORT_DESC])
                ->asArray()
                ->limit(2)
                ->all();
            if($travel_record){
                $data['travel_record']=$travel_record;
            }else{
                $data['travel_record']=[];
            }

            //等待回复数
            $wait_comment_num=StoryComment::find()->where(['from_uid'=>$user_id,'status'=>0])->count();
            $wait_reply_num=StoryCommentReply::find()->where(['reply_to_uid'=>$user_id,'status'=>0])->count();
            $data['wait_reply_num']=$wait_comment_num+$wait_reply_num;

            //通知数
            $data['announce_num']=(int)StoryAnnouncePushLog::find()->where(['user_id'=>$user_id,'status'=>0])->count();

        }else{//否则看已登录的用户个人中心信息

            if(!empty($this->Token)){//有效Token，所以开始找当前登录用户的个人中心信息

                //先检验Token有效性，然后Token换取用户个人基本信息
                $user = Member::findIdentityByAccessToken($this->Token);
                if(!$user){
                    return parent::__response('Token失效不存在',-1);
                }
                $data['user_profile']=[
                    'id'=>$user->id,
                    'username'=>!empty($user->username)?$user->username:'',
                    'picture_url'=>!empty($user->picture_url)?$user->picture_url:'',
                    'nickname'=>!empty($user->nickname)?$user->nickname:'',
                    'signature'=>!empty($user->signature)?$user->signature:'',
                    'real_name_status'=>!empty($user->real_name_status)?$user->real_name_status:0,
                    'mobile'=>!empty($user->mobile)?$user->mobile:'',
                ];

                //先返回 自己关注自己情况
                $data['user_profile']['follower_text']='已关注';
                $data['user_profile']['follower_type']=1;

                //关注数
                $data['follower_count']=(int)Follower::find()->where(['from_user_id'=>$user->id,'follower_type'=>1])->count();
                //热度值
                $data['heart_val_count']=(int)StoryComment::find()->select('sum(heart_val) as heart_val_count')->where(['from_uid'=>$user->id,'is_show'=>1])->groupBy('from_uid')->asArray()->scalar();
                //粉丝数
                $data['fans_count']=(int)Follower::find()->where(['to_user_id'=>$user->id,'follower_type'=>1])->count();

                //我的评论
                $mylast_comment_row=StoryComment::find()
                    ->select(['{{%story_comment}}.id','{{%story_comment}}.title','{{%story_comment_img}}.img_url'])
                    ->leftJoin('{{%story_comment_img}}','{{%story_comment}}.comment_img_id={{%story_comment_img}}.id')
                    ->andWhere(['=', '{{%story_comment}}.from_uid', $user->id])
                    ->andWhere(['=', '{{%story_comment}}.is_show', 1])
                    ->orderBy(['{{%story_comment}}.id'=>SORT_DESC])
                    ->asArray()
                    ->one();
                if($mylast_comment_row){
                    $data['mylast_comment_row']=$mylast_comment_row;
                }else{
                    $data['mylast_comment_row']=[];
                }

                //我的旅行记录  travel_record story
                $travel_record=TravelRecord::find()
                    ->select(['{{%travel_record}}.story_id','{{%travel_record}}.history_chapters','{{%story}}.title','{{%story}}.record_pic'])
                    ->leftJoin('{{%story}}','{{%travel_record}}.story_id={{%story}}.id')
                    ->andWhere(['=', '{{%travel_record}}.user_id', $user->id])
                    ->orderBy(['{{%travel_record}}.id'=>SORT_DESC])
                    ->asArray()
                    ->limit(2)
                    ->all();
                if($travel_record){
                    $data['travel_record']=$travel_record;
                }else{
                    $data['travel_record']=[];
                }

                //等待回复数
                $wait_comment_num=StoryComment::find()->where(['from_uid'=>$user->id,'status'=>0])->count();
                $wait_reply_num=StoryCommentReply::find()->where(['reply_to_uid'=>$user->id,'status'=>0])->count();
                $data['wait_reply_num']=$wait_comment_num+$wait_reply_num;

                //通知数
                $data['announce_num']=(int)StoryAnnouncePushLog::find()->where(['user_id'=>$user->id,'status'=>0])->count();

            }else{//Token user_id都没有，这里主要看Token，所以是Token无效，直接打回去
                throw new \yii\web\UnauthorizedHttpException("Token无效.请重新登录!");
            }

        }

        return parent::__response('ok',0,$data);

    }

    /**
     * 修改昵称 nickname
     */
    public function actionEditNickname()
    {
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        $user_id=Yii::$app->user->getId();
        if(!Yii::$app->request->POST("nickname")||!isset($user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        $nickname = Yii::$app->request->post('nickname');

        $member_model = Member::findOne($user_id);
        $member_model->nickname=$nickname;
        $r=$member_model->save(false);
        if ($r){
            return parent::__response('修改成功',0);
        }else{
            return parent::__response('修改失败',-1);
        }


    }

    /**
     * 修改个性签名 edit_signature
     */
    public function actionEditSignature()
    {
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        $user_id=Yii::$app->user->getId();
        if(!Yii::$app->request->POST("signature")||!isset($user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        $signature = Yii::$app->request->post('signature');

        $member_model = Member::findOne($user_id);
        $member_model->signature=$signature;
        $r=$member_model->save(false);
        if ($r){
            return parent::__response('修改成功',0);
        }else{
            return parent::__response('修改失败',-1);
        }


    }

    /**
     * 关注列表
     */
    public function actionFollowerList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $from_user_id = (int)Yii::$app->request->post('from_user_id');//from_user_id 查看他的关注列表
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断

        if(!isset($from_user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        if(!isset($user_id)) {
            return parent::__response('请先登录!', (int)-1);
        }
        //先看用户是否存在
        $Member_Model=Member::findOne($from_user_id);
        if(!$Member_Model){
            return parent::__response('用户不存在!',(int)-1);
        }
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 5;

        //他的关注
        $Follower_rows=Follower::find()
            ->select(['{{%follower}}.from_user_id','{{%member}}.id','{{%member}}.nickname','{{%member}}.signature','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%follower}}.to_user_id={{%member}}.id')
            ->andWhere(['=', '{{%follower}}.from_user_id', $from_user_id])
            ->orderBy(['{{%follower}}.id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$Follower_rows){
            return parent::__response('暂无数据',(int)0);
        }

        //我的关注
        $me_Follower_rows=Follower::find()
            ->select(['{{%member}}.id'])
            ->leftJoin('{{%member}}','{{%follower}}.to_user_id={{%member}}.id')
            ->andWhere(['=', '{{%follower}}.from_user_id', $user_id])
            ->orderBy(['{{%follower}}.id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$me_Follower_rows){
            //return parent::__response('获取失败',(int)-1);
            $me_Follower_rows=[];
        }

        //我的关注数组id
        $me_follower_arr=array();
        if(is_array($me_Follower_rows)){
            foreach($me_Follower_rows as $k=>$v){
                $me_follower_arr[]=(int)$v['id'];
            }
        }

//        $me_Flollower_str=implode(',',$me_follower_arr);//把我关注的人拼起来

        foreach ($Follower_rows as $kk=>$vv){
                if(in_array((int)$vv['id'],$me_follower_arr)){//echo $vv['id'].'----'.$user_id;exit;
                    $r=Follower::find()
                        ->andWhere(['=', 'from_user_id', (int)$vv['id']])
                        ->andWhere(['=', 'to_user_id', (int)$user_id])
                        ->andWhere(['=', 'follower_type', 1])->one();
                    //var_dump($r);exit;
                    if($r){
                        $Follower_rows[$kk]['follower_status']=2;//已关注
                        $Follower_rows[$kk]['follower_text']='互相关注';
                    }else{
                        $Follower_rows[$kk]['follower_status']=1;//已关注
                        $Follower_rows[$kk]['follower_text']='已关注';
                    }
                }else{
                    $Follower_rows[$kk]['follower_status']=0;//未关注
                    $Follower_rows[$kk]['follower_text']='未关注';
                }
        }

        return parent::__response('ok',0,$Follower_rows);

    }


    /**
     * 粉丝列表 fans_list
     */
    public function actionFansList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $to_user_id = (int)Yii::$app->request->post('to_user_id');//to_user_id 查看他的粉丝列表 关注他的列表
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断

        if(!isset($to_user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        if(!isset($user_id)) {
            return parent::__response('请先登录!', (int)-1);
        }
        //先看用户是否存在
        $Member_Model=Member::findOne($to_user_id);
        if(!$Member_Model){
            return parent::__response('用户不存在!',(int)-1);
        }
        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 5;

        //他的粉丝，关注他的人
        $fans_rows=Follower::find()
            ->select(['{{%follower}}.from_user_id','{{%member}}.id','{{%member}}.nickname','{{%member}}.signature','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%follower}}.from_user_id={{%member}}.id')
            ->andWhere(['=', '{{%follower}}.to_user_id', $to_user_id])
            ->orderBy(['{{%follower}}.id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$fans_rows){
            return parent::__response('暂无数据',(int)0);
        }

        //我的关注
        $me_Follower_rows=Follower::find()
            ->select(['{{%member}}.id'])
            ->leftJoin('{{%member}}','{{%follower}}.to_user_id={{%member}}.id')
            ->andWhere(['=', '{{%follower}}.from_user_id', $user_id])
            ->orderBy(['{{%follower}}.id' => SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$me_Follower_rows){
            $me_Follower_rows=[];
        }

        //我的关注数组id
        $me_follower_arr=array();
        foreach($me_Follower_rows as $k=>$v){
            $me_follower_arr[]=(int)$v['id'];
        }
//        $me_Flollower_str=implode(',',$me_follower_arr);//把我关注的人拼起来

        foreach ($fans_rows as $kk=>$vv){
            if(in_array((int)$vv['id'],$me_follower_arr)){//echo $vv['id'].'----'.$user_id;exit;
                $r=Follower::find()
                    ->andWhere(['=', 'from_user_id', (int)$vv['id']])
                    ->andWhere(['=', 'to_user_id', (int)$user_id])
                    ->andWhere(['=', 'follower_type', 1])->one();
                //var_dump($r);exit;
                if($r){
                    $fans_rows[$kk]['follower_status']=2;//已关注
                    $fans_rows[$kk]['follower_text']='互相关注';
                }else{
                    $fans_rows[$kk]['follower_status']=1;//已关注
                    $fans_rows[$kk]['follower_text']='已关注';
                }
            }else{
                $fans_rows[$kk]['follower_status']=0;//未关注
                $fans_rows[$kk]['follower_text']='未关注';
            }
        }

        return parent::__response('ok',0,$fans_rows);

    }

    /**
     * 点赞列表
     * @params
     *  from_user_id 查看目标评论的人 被点赞列表
     * page 当前页，可选
     * comment_pagenum 查看最新评论故事被点赞的，默认5条
     * reply_pagenum  查看最新回复被点赞的，默认5条
     *
     * 返回有comment_id 你的评论被点赞
     * 返回有reply_id  你的回复被点赞
     */
    public function actionLikeList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $from_user_id = (int)Yii::$app->request->post('from_user_id');//from_user_id 查看目标评论的人 被点赞列表
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断

        if(!isset($from_user_id)){
            return parent::__response('参数错误!',(int)-2);
        }
        if(!isset($user_id)) {
            return parent::__response('请先登录!', (int)-1);
        }
        //先看用户是否存在
        $Member_Model=Member::findOne($from_user_id);
        if(!$Member_Model){
            return parent::__response('用户不存在!',(int)-1);
        }
        $page = (int)Yii::$app->request->post('page');//当前页
        $comment_pagenum = (int)Yii::$app->request->post('comment_pagenum');//一页显示多少
        $reply_pagenum = (int)Yii::$app->request->post('reply_pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($comment_pagenum < 1) $comment_pagenum = 5;
        if ($reply_pagenum < 1) $reply_pagenum = 5;

        //story_comment story_comment_like_log member
        //////////////////查看目标用户对故事发表过的评论id数组///////////////////////
        $from_comment_id_arr=StoryComment::find()->select(['id'])->andWhere(['from_uid'=>$from_user_id])->asArray()->all();
        $comment_id_arr=[];
        foreach ($from_comment_id_arr as $k=>$v){
            $comment_id_arr[]=(int)$v['id'];
        }
        //var_dump($usr_id_arr);exit;
        //他发布的评论被点赞的列表
        $like_rows1=array();
        $like_rows1=StoryCommentLikeLog::find()
            ->select(['{{%story_comment_like_log}}.comment_id','{{%story_comment_like_log}}.create_at','{{%story_comment_like_log}}.user_id','{{%member}}.nickname','{{%member}}.signature','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%story_comment_like_log}}.user_id={{%member}}.id')
            ->andWhere(['in', '{{%story_comment_like_log}}.comment_id', $comment_id_arr])
            ->orderBy(['{{%story_comment_like_log}}.id' => SORT_DESC])
            ->offset($comment_pagenum * ($page - 1))
            ->limit($comment_pagenum)
            ->asArray()
            ->all();
        if(!is_array($like_rows1)){
            $like_rows1=[];
        }

        //story_comment_reply story_comment_reply_like_log member
        ///////////////////////////查看目标用户对评论发表过的回复评论id数组//////////////////
        $from_reply_id_arr=StoryCommentReply::find()->select(['id'])->andWhere(['reply_from_uid'=>$from_user_id])->asArray()->all();
        $reply_id_arr=[];
        foreach ($from_reply_id_arr as $k=>$v){
            $reply_id_arr[]=(int)$v['id'];
        }
        //var_dump($usr_id_arr);exit;
        //他发布的评论被点赞的列表
        $like_rows2=array();
        $like_rows2=StoryCommentReplyLikeLog::find()
            ->select(['{{%story_comment_reply_like_log}}.reply_id','{{%story_comment_reply_like_log}}.create_at','{{%story_comment_reply_like_log}}.user_id','{{%member}}.nickname','{{%member}}.signature','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%story_comment_reply_like_log}}.user_id={{%member}}.id')
            ->andWhere(['in', '{{%story_comment_reply_like_log}}.reply_id', $reply_id_arr])
            ->orderBy(['{{%story_comment_reply_like_log}}.id' => SORT_DESC])
            ->offset($reply_pagenum * ($page - 1))
            ->limit($reply_pagenum)
            ->asArray()
            ->all();
        if(!is_array($like_rows2)){
            $like_rows2=[];
        }

        //评论故事的和回复的合并
        $like_rows=array_merge($like_rows1,$like_rows2);
        //var_dump($like_rows);exit;
        //在排时间回复排序
        $a = array();
        foreach($like_rows as $key=>$val){
            $a[] = (int)$val['create_at'];//这里要注意$val['nums']不能为空，不然后面会出问题
        }
        //$a先排序
        rsort($a);
        $a = array_flip($a);
        $result = array();
        foreach($like_rows as $k=>$v){
            $temp1 = $v['create_at'];
            $temp2 = $a[$temp1];
            $result[$temp2] = $v;
        }
        ksort($result);//这里还要把$result进行排序，健的位置不对

        return parent::__response('ok',0,$result);

    }

    /*
     * 关注&取消关注
     *@params
     * to_user_id 目标用户
     * follower_type 操作类型  0取消关注 1关注 2拉黑
     */
    public function actionFollowing(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $to_user_id = (int)Yii::$app->request->post('to_user_id');//目标用户
        $follower_type = (int)Yii::$app->request->post('follower_type');//操作类型
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断

        if(!isset($to_user_id)||!isset($follower_type)){
            return parent::__response('参数错误!',(int)-2);
        }
        if(!isset($user_id)) {
            return parent::__response('请先登录!', (int)-1);
        }

        //先看目标用户是否存在
        $Member_Model=Member::findOne($to_user_id);
        if(!$Member_Model){
            return parent::__response('用户不存在!',(int)-1);
        }
        //看看目前关注的状态
        $follower_model=Follower::find()->andWhere(['from_user_id'=>$user_id,'to_user_id'=>$to_user_id])->one();
        $mes='操作';
        if($follower_model){//如果有记录

            //进行操作
            if($follower_type==1){//关注
                if($follower_model->follower_type==1){return parent::__response('已经关注，不能在关注了!',(int)-1);}
                $follower_model->follower_type=1;
                $mes='关注';
            }elseif($follower_type==2){//拉黑
                if($follower_model->follower_type==2){return parent::__response('已经拉黑，不能在拉黑了!',(int)-1);}
                $follower_model->follower_type=2;
                $mes='拉黑';
            }elseif($follower_type==0){//取消关注
                if($follower_model->follower_type==0){return parent::__response('已经取消关注，不能在取消了!',(int)-1);}
                $follower_model->follower_type=0;
                $mes='取消关注';
            }else{
                return parent::__response('参数错误!',(int)-2);
            }
            $r=$follower_model->save(false);
            if($r){
                return parent::__response($mes.'成功',(int)0);
            }else{
                return parent::__response($mes.'失败',(int)-1);
            }

        }else{//如果没记录

            //进行操作
            if($follower_type==1){//关注
                $_follower_model=new Follower();
                $_follower_model->to_user_id=$to_user_id;
                $_follower_model->follower_type=1;
                $mes='关注';
            }elseif($follower_type==2){//拉黑
                $_follower_model=new Follower();
                $_follower_model->to_user_id=$to_user_id;
                $_follower_model->follower_type=2;
                $mes='拉黑';
            }else{//取消关注
                return parent::__response('你并没有关注，不能取消!',(int)-1);
            }
            $_follower_model->from_user_id=$user_id;
            $_follower_model->created_at=time();
            $isValid = $_follower_model->validate();
            if($isValid){
                $r=$_follower_model->save();
                if($r){
                    return parent::__response($mes.'成功',(int)0);
                }else{
                    return parent::__response($mes.'失败',(int)-1);
                }
            }else{
                return parent::__response('参数错误',(int)-1);
            }

        }

    }

    /**
     * 上传个人头像
     * 'enctype' => 'multipart/form-data' 图片文件传参： UploadForm[picture_url]
     */
    public function actionUploadPhoto()
    {
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断
        if(!isset($user_id)) {
            return parent::__response('请先登录!', (int)-1);
        }

        $model = new UploadForm();
        $model->imageFile = UploadedFile::getInstance($model, 'picture_url');
        $picture_url=$model->upload(true,400,400);
        if($picture_url){
            //保存头像地址
            $member_model = Member::findOne($user_id);
            $member_model->picture_url=Yii::getAlias('@static').'/'.$picture_url;
            $r=$member_model->save(false);
            if ($r){
                return parent::__response('上传成功',0);
            }else{
                return parent::__response('上传失败',-1);
            }
        }

    }

    /**
     *
     * 登录、绑定手机发送验证码
     * mobile string
     * send_type   发送类型 1登录验证码 2绑定手机 3通知\默认不传为1
     */
   public function actionSendSms(){
       if(!Yii::$app->request->isPost){//如果不是post请求
           return parent::__response('Request Error!',(int)-1);
       }
       if(!Yii::$app->request->post('mobile')){
           return parent::__response('手机号不能为空',(int)-2);
       }
       $mobile =Yii::$app->request->post('mobile')+0;
       $send_type =(int)Yii::$app->request->post('send_type');//发送类型 1登录验证码 2绑定手机 3通知
        if(!$send_type){
            $send_type=1;
        }
        if(!in_array($send_type,[1,2,3])){
            return parent::__response('参数错误，不支持此发送类型',(int)-2);
        }

       if (!preg_match("/^[1][34589][0-9]{9}$/", $mobile)) {
           return parent::__response('手机号格式错误，请重新输入！',(int)-2);
       }

       if($send_type==2){//如果要绑定手机发短信
           //查看此手机是否有绑定过
           $mobile_model=Member::find()->andWhere(['mobile'=>$mobile])->one();
           if($mobile_model){
               return parent::__response('操作失败！此手机已经绑定过了',(int)-1);
           }
       }

       //查看最后一次发短信的时间，限制一个手机发短信的时间频率
       $last_send_time=SmsLog::find()->select(['created_at'])->andWhere(['mobile'=>$mobile])->andWhere(['send_type'=>$send_type])->orderBy(['id'=>SORT_DESC])->scalar();
       if($last_send_time){
           if((time()-$last_send_time)<Yii::$app->params['sendsms_code_time']*60){
               return parent::__response('短信已发出，请稍后在试!',(int)-1);
           }
       }
        //Yii::$app->request->getUserIP();exit;

       //发短信接口操作
       $send_flag=true;
       $SendSms_model=new SendSms;
       $code=rand(100000,999999);
       $response = $SendSms_model->sendSms($mobile,$code);
       if($response->Code!='OK'){
           return parent::__response('发送失败',(int)-1,['send_Message'=>$response->Message,'send_Code'=>$response->Code,'RequestId'=>$response->RequestId]);
       }

       if($send_flag){
           $sms_model=new SmsLog();
           $sms_model->mobile=$mobile;
           $sms_model->code=$code;
           $sms_model->status=1;//发送成功
           $sms_model->send_type=$send_type;//发送类别验证码
           $sms_model->created_at=time();

           //验证保存
           $isValid = $sms_model->validate();
           if ($isValid) {
               $r=$sms_model->save();
               if($r){
                   return parent::__response('发送成功,请在'.Yii::$app->params['sendsms_code_time'].'分钟之内及时验证！',(int)0);
               }else{
                   return parent::__response('发送失败!',(int)-1);
               }
           }else{
               return parent::__response('参数错误!',(int)-2);
           }
       }else{
           return parent::__response('发送失败',(int)-1);
       }

    }

    /**
     *
     * 手机登录
     * mobile string
     */
    public function actionMobileLogin(){
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->post('mobile')||!Yii::$app->request->post('code')){
            return parent::__response('手机号不能为空',(int)-2);
        }
        $mobile =Yii::$app->request->post('mobile')+0;
        $code =(int)Yii::$app->request->post('code');

        if (!preg_match("/^[1][34589][0-9]{9}$/", $mobile)) {
            return parent::__response('手机号格式错误，请重新输入！',(int)-2);
        }

        //先去看库里有没有此手机发的短信，且短信验证码没有过期
        //$sms_model=SmsLog::find()->andWhere(['mobile'=>$mobile,'status'=>1,'send_type'=>1])->andWhere(['in' , 'code' , [$code,123456]])->orderBy(['id'=>SORT_DESC])->one();
        $sms_model=SmsLog::find()->andWhere(['mobile'=>$mobile,'status'=>1,'send_type'=>1])->orderBy(['id'=>SORT_DESC])->one();//->andWhere(['in' , 'code' , [$code,123456]])
        if(!$sms_model){
            return parent::__response('手机号无效,请重新发送短信验证!',(int)-1);
        }

        //如果不是特例的验证码就要验证下
        //if($code!=123456){
            if($sms_model->code!=$code){
                return parent::__response('验证码错误!',(int)-1);
            }
            if((time()-$sms_model->created_at)>Yii::$app->params['sendsms_code_time']*60){//检验验证码是否过期
                return parent::__response('验证码已过期,请重新发送短信验证!',(int)-2);
            }
        //}

        //查看用户是否有注册在用户表
        //$member_model=Member::find()->andWhere(['mobile'=>$mobile])->one();
        $member_model=Member::findByMobileUser($mobile);
        if(!$member_model){///如果没有，注册一个
            $user = new Member();
            $user->username = $mobile;
            $user->mobile = $mobile;
            $user->nickname =substr($mobile, 0, 3).'****'.substr($mobile, 7, 4);
            $user->picture_url=Yii::getAlias('@static').'/uploads/default/avatar.png';//头像
            //$user->setPassword($mobile);
            //$user->generateAuthKey();
            $r=$user->save(false);
            if(!$r){//注册失败
                return parent::__response('登录失败',(int)-1);
            }
        }

        //在登录一把返回Token
        $MobileLoginForm_model = new MobileLoginForm;
        $MobileLoginForm_model->mobile=$mobile;
        if ($user = $MobileLoginForm_model->mobile_login()) {
            if ($user instanceof IdentityInterface) {
                //return $user->api_token;
                $user_profile=[
                    'id'=>$user->id,
                    'username'=>!empty($user->username)?$user->username:'',
                    'picture_url'=>!empty($user->picture_url)?$user->picture_url:'',
                    'nickname'=>!empty($user->nickname)?$user->nickname:'',
                    'signature'=>!empty($user->signature)?$user->signature:'',
                    'real_name_status'=>!empty($user->real_name_status)?$user->real_name_status:0,
                    'mobile'=>!empty($user->mobile)?$user->mobile:'',
                    //'api_token'=>!empty($user->api_token)?$user->api_token:'',
                ];
                return parent::__response('登录成功',0,['user_profile'=>$user_profile,'api_token'=>$user->api_token]);//Token在里面
            } else {
                return $user->errors;
            }
        } else {
            return $MobileLoginForm_model->errors;
            //var_dump($user->errors);
            //return parent::__response($user->errors,-1);
        }


    }

    /**
     *Time:2020/9/10 15:06
     *Author:始渲
     *Remark:第三方登录
     * @params:
     * third_key 第三方登录唯一标识id，什么openid client_id open_id 啥的!
     * third_type 三方登陆类型 1微信 2QQ 3微博
     * nickname 昵称 授权以后的名字
     * headimgurl 头像 授权以后的头像地址
     */
    public function actionThirdLogin(){
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->post('third_key')||!Yii::$app->request->post('third_type')||!Yii::$app->request->post('nickname')||!Yii::$app->request->post('headimgurl')){
            return parent::__response('参数错误',(int)-2);
        }
        $third_key =Yii::$app->request->post('third_key');
        $third_type =(int)Yii::$app->request->post('third_type');
        $nickname =Yii::$app->request->post('nickname');
        $headimgurl =Yii::$app->request->post('headimgurl');

        if(!in_array($third_type,[1,2,3])){//如果不是授权的范围内登录类型
            return parent::__response('参数错误，不在授权范围内',(int)-2);
        }

     //////////////判断一下是否是第一次授权登录，然后去自动注册一把/////////////
        $MemberAuths_model=MemberAuths::find()->andWhere(['third_key'=>$third_key])->one();//->andWhere(['in' , 'code' , [$code,123456]])
        if(!$MemberAuths_model){
            //看一下有没有非法参数third_key注册过member表的username字段，而auths表没记录
            $Member_model=Member::find()->select(['username'])->andWhere(['username'=>$third_key])->scalar();
            if($Member_model){return parent::__response('非法参数错误，third_key已经有用户记录了！',(int)-2);}


            ///////首先没有记录是第一次登录，先去注册一个空账号
            $transaction=Yii::$app->db->beginTransaction();//开启事务
            $Member_model = new Member();
            $Member_model->username = $third_key;//暂时用key作username接下来的登录
            //$Member_model->mobile = $mobile;
            //$Member_model->setPassword($mobile);
            //$Member_model->generateAuthKey();
            $Member_model->nickname=$nickname;//昵称
            $Member_model->picture_url=$headimgurl;//头像
            $r=$Member_model->save(false);
            if(!$r){
                return parent::__response('登录失败',(int)-1);
            }
            $newe_member_id=$Member_model->attributes['id'];//生成新的user_id
            if(!$newe_member_id){//注册失败
                return parent::__response('登录失败',(int)-1);
            }

            ///////然后在去注册一个第三方member_auths表，关联新注册的账号
            $member_Auths_Model=new MemberAuths();
            $member_Auths_Model->user_id=$newe_member_id;
            $member_Auths_Model->third_key=$third_key;
            $member_Auths_Model->third_type=$third_type;
            $member_Auths_Model->created_at=time();
            $auth_r=$member_Auths_Model->save(false);
            if(!$auth_r){
                $transaction->rollBack();//回滚事务
                return parent::__response('登录失败',(int)-1);
            }
            $transaction->commit();//提交事务
            $username=$Member_model->username;//拿到username去登录，这里的username就是第三方唯一id $third_key
        }else{
            //已经授权登录过了，去member表找到username去登录
            $username='';
            $username=Member::find()->select(['username'])->where(['id'=>$MemberAuths_model->user_id])->scalar();
        }

        if(!$username){//如果不是post请求
            return parent::__response('账号异常，登录失败！',(int)-1);
        }


        //登录一把返回Token
        $ThirdLoginForm_model = new ThirdLoginForm;
        $ThirdLoginForm_model->username=$username;
        if ($user = $ThirdLoginForm_model->username_login()) {
            if ($user instanceof IdentityInterface) {
                $user_profile=[
                    'id'=>$user->id,
                    'username'=>!empty($user->username)?$user->username:'',
                    'picture_url'=>!empty($user->picture_url)?$user->picture_url:'',
                    'nickname'=>!empty($user->nickname)?$user->nickname:'',
                    'signature'=>!empty($user->signature)?$user->signature:'',
                    'real_name_status'=>!empty($user->real_name_status)?$user->real_name_status:0,
                    'mobile'=>!empty($user->mobile)?$user->mobile:'',
                    'third_type'=>$third_type,
                    //'api_token'=>!empty($user->api_token)?$user->api_token:'',
                ];
                return parent::__response('登录成功',0,['user_profile'=>$user_profile,'api_token'=>$user->api_token]);//Token在里面
            } else {
                return $user->errors;
            }
        } else {
            return $ThirdLoginForm_model->errors;
        }

    }

    /**
     *Time:2020/9/29 14:30
     *Author:始渲
     *Remark:第三方登录后绑定手机 需要登录状态下
     * @params:
     * mobile 手机号
     * code 验证码
     * third_type 1微信 2 QQ 3微博
     */
    public function actionThirdBindMobile(){
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->post('mobile')||!Yii::$app->request->post('code')||!Yii::$app->request->post('third_type')){
            return parent::__response('参数错误',(int)-2);
        }
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断
        if(!isset($user_id)) {
            return parent::__response('请先登录在绑定!', (int)-1);
        }

        $mobile =Yii::$app->request->post('mobile')+0;
        $code =(int)Yii::$app->request->post('code');
        $third_type =(int)Yii::$app->request->post('third_type');

        if (!preg_match("/^[1][34589][0-9]{9}$/", $mobile)) {
            return parent::__response('手机号格式错误，请重新输入！',(int)-2);
        }

        if(!in_array($third_type,[1,2,3])){//1微信 2 QQ 3微博
            return parent::__response('参数错误，不支持此绑定类型',(int)-2);
        }

        //先去看库里有没有此手机发的短信，且短信验证码没有过期
        $sms_model=SmsLog::find()->andWhere(['mobile'=>$mobile,'status'=>1,'send_type'=>2])->orderBy(['id'=>SORT_DESC])->one();
        if(!$sms_model){
            return parent::__response('手机号无效,请重新发送短信验证!',(int)-1);
        }

        //如果不是特例的验证码就要验证下
        //if($code!=123456){
            if($sms_model->code!=$code){
                return parent::__response('验证码错误!',(int)-1);
            }
            if((time()-$sms_model->created_at)>Yii::$app->params['sendsms_code_time']*60){//检验验证码是否过期
                return parent::__response('验证码已过期,请重新发送短信验证!',(int)-2);
            }
        //}

        //查看此手机是否有绑定过
        $mobile_model=Member::find()->andWhere(['mobile'=>$mobile])->one();
        if($mobile_model){
            return parent::__response('操作失败！此手机已经绑定过了',(int)-1);
        }

        //查看用户是否有注册在用户表
        $member_model=Member::findOne($user_id);
        if(!$member_model){
            return parent::__response('登录用户不存在',(int)-1);
        }
        //查看第三方登录表是否有记录
        $MemberAuths_model=MemberAuths::find()->andWhere(['user_id'=>$user_id,'third_type'=>$third_type])->one();
        if(!$MemberAuths_model){
            return parent::__response('第三方登录用户不存在',(int)-1);
        }

        //更新用户表的手机号码
        $member_model->mobile=$mobile;
        $r=$member_model->save(false);
        if($r){
            return parent::__response('绑定成功',0);
        }else{
            return parent::__response('登录失败',(int)-1);
        }

    }

    /**
     *Time:2020/9/29 14:30
     *Author:始渲
     *Remark:手机登录后绑定第三方 需要登录状态下
     * @params:
     * third_key 第三方登录唯一标识id，什么openid client_id open_id 啥的!
     * third_type 三方登陆类型 1微信 2QQ 3微博
     */
    public function actionMobileBindThird(){
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->post('third_key')||!Yii::$app->request->post('third_type')){
            return parent::__response('参数错误',(int)-2);
        }
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断
        if(!isset($user_id)) {
            return parent::__response('请先登录在绑定!', (int)-1);
        }

        //查看用户是否有注册在用户表
        $member_model=Member::findOne($user_id);
        if(!$member_model){
            return parent::__response('登录用户不存在',(int)-1);
        }

        $third_key =Yii::$app->request->post('third_key');
        $third_type =(int)Yii::$app->request->post('third_type');

        if(!in_array($third_type,[1,2,3])){//1微信 2 QQ 3微博
            return parent::__response('参数错误，不支持此绑定类型',(int)-2);
        }

        //查看此手机登录用户是否有绑定过 此类型第三方
        $mobile_model=MemberAuths::find()->andWhere(['user_id'=>$user_id,'third_type'=>$third_type])->one();
        if($mobile_model){
            return parent::__response('操作失败！此登录用户已经绑定过了',(int)-1);
        }
        //查看第三方登录是否有绑定其它的记录
        $MemberAuths_model=MemberAuths::find()->andWhere(['third_key'=>$third_key,'third_type'=>$third_type])->one();
        if($MemberAuths_model){
            return parent::__response('第三方登录用户已经绑定其它的了',(int)-1);
        }

        ///////先去新增一个第三方member_auths表的记录，关联已经手机登录的账号
        $member_Auths_Model=new MemberAuths();
        $member_Auths_Model->user_id=$user_id;
        $member_Auths_Model->third_key=$third_key;
        $member_Auths_Model->third_type=$third_type;
        $member_Auths_Model->created_at=time();
        $auth_r=$member_Auths_Model->save(false);
        if($auth_r){
            return parent::__response('绑定成功',0);
        }else{
            return parent::__response('绑定失败',(int)-1);
        }

    }

    /**
     *Time:2020/9/11 17:30
     *Author:始渲
     *Remark:评论的回复列表
     * @params: Token $page $pagenum
     *
     */
    public function actionMyReplyList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断

        if(!isset($user_id)) {
            throw new \yii\web\UnauthorizedHttpException("Token无效.请重新登录!");
        }

        //先看目标用户是否存在
        $Member_Model=Member::findOne($user_id);
        if(!$Member_Model){
            return parent::__response('用户不存在!',(int)-1);
        }

        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 10;

        $StoryCommentReply_rows=StoryCommentReply::find()
            ->select(['{{%story_comment_reply}}.*','{{%member}}.username','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%story_comment_reply}}.reply_from_uid={{%member}}.id')
            ->andWhere(['=', '{{%story_comment_reply}}.reply_to_uid', $user_id])
            //->andWhere(['=', '{{%story_comment_reply}}.reply_type', 1])//1对评论发布回复 2对回复发布回复 3@人+对回复发布回复
            ->andWhere(['=', '{{%story_comment_reply}}.is_show', 1])
            ->orderBy(['id'=>SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$StoryCommentReply_rows){
            return parent::__response('暂无回复评论',(int)0,[]);
        }

        return parent::__response('ok',0,$StoryCommentReply_rows);

    }

    /**
     *Time:2020/9/11 18:07
     *Author:始渲
     *Remark:个人中心我的评论回复详情
     * @params: reply_id
     */
    public function actionMyReplyDetails(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("reply_id")){
            return parent::__response('参数错误!',(int)-2);
        }
        $reply_id = (int)Yii::$app->request->post('reply_id');

        $StoryCommentReply_row=StoryCommentReply::find()
            ->select(['{{%story_comment}}.story_id','{{%story_comment}}.content as comment_content','{{%story_comment_reply}}.comment_id','{{%story_comment_reply}}.reply_from_uid','{{%story_comment_reply}}.reply_content','{{%story_comment_reply}}.reply_at','{{%story_comment_reply}}.likes','{{%member}}.username','{{%member}}.picture_url'])
            ->leftJoin('{{%member}}','{{%story_comment_reply}}.reply_from_uid={{%member}}.id')
            ->leftJoin('{{%story_comment}}','{{%story_comment_reply}}.comment_id={{%story_comment}}.id')
            ->andWhere(['=', '{{%story_comment_reply}}.id', $reply_id])
            ->andWhere(['=', '{{%story_comment_reply}}.reply_type', 1])
            ->asArray()
            ->one();

        //找到游戏名称标签
        $game_title=Story::find()->select(['game_title'])->where(['id'=>$StoryCommentReply_row['story_id']])->scalar();
        
        if($game_title){
            $StoryCommentReply_row['game_title']=$game_title;
        }else{
            $StoryCommentReply_row['game_title']='';
        }

        if($StoryCommentReply_row){
            return parent::__response('ok',0,$StoryCommentReply_row);
        }else{
            return parent::__response('暂无回复评论',(int)-0);
        }


    }


    /**
     *Time:2020/12/11 9:26
     *Author:始渲
     *Remark:个人中心 我的评论列表/Ta的评论列表
     * @params:
     * user_id  如果传入user_id查看指定用户的评论列表，不传则看登录用户的
     * page 当前页
     * pagenum 一页显示多少
     */
    public function actionMyCommentList(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }

        $to_user_id = (int)Yii::$app->request->post('user_id');//指定用户的user_id
        if($to_user_id>0){//如果有传入指定用户的user_id
            $user_id=$to_user_id;
        }else{//否则看登录用户的 user_id
            $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断
        }

        if(!isset($user_id)) {
            throw new \yii\web\UnauthorizedHttpException("Token无效.请重新登录!");
        }

        //先看目标用户是否存在
        $Member_Model=Member::findOne($user_id);
        if(!$Member_Model){
            return parent::__response('用户不存在!',(int)-1);
        }

        $page = (int)Yii::$app->request->post('page');//当前页
        $pagenum = (int)Yii::$app->request->post('pagenum');//一页显示多少
        if ($page < 1) $page = 1;
        if ($pagenum < 1) $pagenum = 10;

        $StoryComment_rows=StoryComment::find()
            ->select(['{{%story_comment}}.*','{{%member}}.picture_url','{{%story_comment_img}}.img_url as comment_img_url','{{%story}}.game_title'])
            ->leftJoin('{{%member}}','{{%story_comment}}.from_uid={{%member}}.id')
            ->leftJoin('{{%story_comment_img}}','{{%story_comment}}.comment_img_id={{%story_comment_img}}.id')
            ->leftJoin('{{%story}}','{{%story_comment}}.story_id={{%story}}.id')
            ->andWhere(['=', '{{%story_comment}}.from_uid', $user_id])
            ->andWhere(['=', '{{%story_comment}}.is_show', 1])
            ->orderBy(['id'=>SORT_DESC])
            ->offset($pagenum * ($page - 1))
            ->limit($pagenum)
            ->asArray()
            ->all();
        if(!$StoryComment_rows){
            return parent::__response('暂无评论',(int)0,[]);
        }

        return parent::__response('ok',0,$StoryComment_rows);

    }


    /**
     *Time:2020/9/27 11:07
     *Author:始渲
     *Remark: 实名认证
     * @params:
     * real_name string 实名认证名字
     * real_idCard string 实名认证身份证号
     */
    public function actionRealAuthentication()
    {
        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        $user_id = (int)Yii::$app->user->getId();//已登录的用户，Token判断
        if(!isset($user_id)) {
            return parent::__response('请先登录!', (int)-1);
        }

        if(!Yii::$app->request->post('real_name')||!Yii::$app->request->post('real_idCard')){
            return parent::__response('参数错误',(int)-2);
        }
        $real_name =Yii::$app->request->post('real_name');
        $real_idCard =Yii::$app->request->post('real_idCard');

        //先看是否已经实名认证过了
        $member_model = Member::findOne($user_id);
        if($member_model->real_name_status==1){
            return parent::__response('已经认证过了，不能重复认证!', (int)-1);
        }


        //////调第三方接口去认证////
        $date=date("YmdHis");
        $str=Yii::$app->params['ytx_139130']['Account_sid'].Yii::$app->params['ytx_139130']['Auth_token'].$date;
        $sigParameter=sha1($str,false);

        $str2=Yii::$app->params['ytx_139130']['Account_sid'].':'.$date;
        $Authorization=base64_encode($str2);

        $url='https://www.139130.com/ytx-api/v1.0.0/n-meta/verify/2meta?sig='.$sigParameter;

        $data_arr=['uuid'=>uniqid(),'metas'=>[['name'=>$real_name,'idCard'=>$real_idCard]]];
        $data  = json_encode($data_arr);
        //$data='{"uuid": "'.uniqid().'","metas": [{"name": "文建波","idCard": "510524199210025476"}]}';

        $headerArray =array("Content-type:application/json;charset='utf-8'","Accept:application/json",'Authorization:'.$Authorization);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl,CURLOPT_HTTPHEADER,$headerArray);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        $output_arr=json_decode($output,true);

        if($output_arr['code']==0){
                if($output_arr['metas'][0]['resultCode']==200){
                    $member_model->real_name_status=1;
                    $member_model->real_name=$real_name;
                    $member_model->real_idCard=$real_idCard;
                    $r=$member_model->save(false);
                    if ($r){
                        return parent::__response('认证成功',0);
                    }else{
                        return parent::__response('认证失败',-1);
                    }
                }elseif($output_arr['metas'][0]['resultCode']==201){
                    return parent::__response('认证失败,信息不匹配',-1);
                }elseif($output_arr['metas'][0]['resultCode']==202){
                    return parent::__response('认证失败,查无此身份证',-1);
                }else{
                    return parent::__response('认证失败',-1);
                }
        }else{
            return parent::__response('认证失败',-1);
        }

    }

    /**
     *Time:2020/11/26 10:10
     *Author:始渲
     *Remark:添加举报
     * @params:
     * report_to_uid 被举报用户id 必传
     * type     举报来源类型 0其它 1评论 2回复 3用户个人中心, 默认不传是1评论
     * event_id 举报的事件业务id comment_id或者 reply_id，可以不传
     * title    举报标题，可以不传
     * content  举报内容，可以不传
     */
    public function actionReportAdd(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("report_to_uid")){
            return parent::__response('参数错误!',(int)-2);
        }

        $report_to_uid = (int)Yii::$app->request->post('report_to_uid');//被举报用户id
        //举报来源类型 0其它 1评论 2回复 3用户个人中心, 默认不传是1评论
        if(in_array((int)Yii::$app->request->post('type'),[0,1,2,3])){
            $type=(int)Yii::$app->request->post('type');
        }else{
            $type=1;
        }
        //举报的事件业务id comment_id或者 reply_id
        if((int)Yii::$app->request->post('event_id')>0){
            $event_id=Yii::$app->request->post('event_id');
        }else{
            $event_id=0;
        }
        //举报标题
        if(!empty(Yii::$app->request->post('title'))){
            $title=Yii::$app->request->post('title');
        }else{
            $title='';
        }
        //举报内容
        if(!empty(Yii::$app->request->post('content'))){
            $content=Yii::$app->request->post('content');
        }else{
            $content='';
        }

        //先看被举报用户是否存在
        $Member_Model=Member::findOne($report_to_uid);
        if(!$Member_Model){
            return parent::__response('用户不存在!',(int)-1);
        }

        $Report_model=new Report();
        $Report_model->report_from_uid = Yii::$app->user->getId();//举报人id
        $Report_model->report_to_uid = $report_to_uid;//被举报用户id
        $Report_model->type = $type;//举报来源类型 0其它 1评论 2回复 3用户个人中心
        $Report_model->event_id = $event_id;//举报的事件业务id comment_id或者 reply_id
        $Report_model->title = $title;//举报标题
        $Report_model->content = $content;//举报内容
        $Report_model->status = 0;//举报审核状态 0未审核 1已审核
        $Report_model->created_at=time();//创建时间

        //验证保存
        $isValid = $Report_model->validate();
        if ($isValid) {
            $r=$Report_model->save();
            if($r){
                $report_id=$Report_model->id;
                return parent::__response('举报成功',(int)0,['report_id'=>$report_id]);
            }else{
                return parent::__response('举报失败!',(int)-1);
            }
        }else{
            return parent::__response('参数错误!',(int)-2);
        }

    }

    /**
     *Time:2020/11/26 11:40
     *Author:始渲
     *Remark:屏蔽添加，需验证Token登录
     * @params:
     * shield_to_uid 被屏蔽用户id
     */
    public function actionShieldAdd(){

        if(!Yii::$app->request->isPost){//如果不是post请求
            return parent::__response('Request Error!',(int)-1);
        }
        if(!Yii::$app->request->POST("shield_to_uid")){
            return parent::__response('参数错误!',(int)-2);
        }

        $shield_from_uid=Yii::$app->user->getId();//操作屏蔽用户id
        $shield_to_uid = (int)Yii::$app->request->post('shield_to_uid');//被屏蔽用户id

        //先看被屏蔽用户是否存在
        $Member_Model=Member::findOne($shield_to_uid);
        if(!$Member_Model){
            return parent::__response('屏蔽用户不存在!',(int)-1);
        }

        //检测是否有屏蔽操作
        $Shield_Model=Shield::find()->andWhere(['shield_from_uid'=>$shield_from_uid,'shield_to_uid'=>$shield_to_uid])->one();
        if($Shield_Model){
            if($Shield_Model->status==1){
                return parent::__response('已经屏蔽操作过了!',(int)-1);
            }else{
                $Shield_Model->status = 1;
                $r=$Shield_Model->save(false);
                if($r){
                    $shield_id=$Shield_Model->id;
                    return parent::__response('屏蔽成功',(int)0,['shield_id'=>$shield_id]);
                }else{
                    return parent::__response('屏蔽失败!',(int)-1);
                }
            }
        }

        //否则创建新的屏蔽操作记录
        $_Shield_model=new Shield();
        $_Shield_model->shield_from_uid = $shield_from_uid;//操作屏蔽用户id
        $_Shield_model->shield_to_uid = $shield_to_uid;//被屏蔽用户id
        $_Shield_model->status = 1;//屏蔽状态 0已取消屏蔽 1已屏蔽
        $_Shield_model->created_at=time();//创建时间

        //验证保存
        $isValid = $_Shield_model->validate();
        if ($isValid) {
            $r=$_Shield_model->save();
            if($r){
                $shield_id=$_Shield_model->id;
                return parent::__response('屏蔽成功',(int)0,['shield_id'=>$shield_id]);
            }else{
                return parent::__response('屏蔽失败!',(int)-1);
            }
        }else{
            return parent::__response('参数错误!',(int)-2);
        }

    }

	/**
	 * test
     * UploadForm[imageFile]
	 */
	public function actionTest ()
	{

//        $img_all_url='uploads/20200916/20200916_axe5f618d4f005929.12677713.jpg';
//        $image = \yii\imagine\Image::thumbnail(Yii::getAlias($img_all_url), 100, 100, \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND);
//        $r=$image->save('uploads/20200916/thumb_20200916_axe5f618d4f005929.12677713.jpg');
//        if($r){
//            //echo  'small-' . $filename;
//            //$uparr['thumb_url']=$host_dir . '/thumb-' . $filename;
//            echo 111;
//        }else{
//            echo 000;
//        }

//	    return [
//	        'xxxxxxxxxxxxx',
//	    ];
//        $model = new UploadForm();
//
//        if (Yii::$app->request->isPost) {
//            //return file_get_contents('php://input');exit;
//            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
//            if ($model->upload()) {
//                // 文件上传成功
//                return 'ok';
//            }else{
//                return '0';
//            }
//        }

//        $SendSms_model=new SendSms;
//        $response = $SendSms_model->sendSms(18201458982,999999);
//        if($response->Code!='OK'){
//           return parent::__response('发送失败',(int)-1,['send_Message'=>$response->Message,'send_Code'=>$response->Code,'RequestId'=>$response->RequestId]);
//       }
    echo 222;

	}

}