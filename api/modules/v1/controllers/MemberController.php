<?php

namespace api\modules\v1\controllers;

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

	    $user_profile=[
	        'id'=>$user->id,
            'username'=>$user->username,
            'picture_url'=>$user->picture_url,
            'nickname'=>$user->nickname,
            'signature'=>$user->signature,
            'real_name_status'=>$user->real_name_status,
            'mobile'=>$user->mobile,
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
        $headers = Yii::$app->getRequest()->getHeaders();
        $token = $headers->get('token');
        //$user_id=Yii::$app->user->getId();
        $data=array();

        if($token){//如果有Token用户自己看自己
            //用户个人基本信息
            $user = Member::findIdentityByAccessToken($token);
            $data['user_profile']=[
                'id'=>$user->id,
                'username'=>$user->username,
                'picture_url'=>$user->picture_url,
                'nickname'=>$user->nickname,
                'signature'=>$user->signature,
                'real_name_status'=>$user->real_name_status,
                'mobile'=>$user->mobile,
            ];
            //我的评论
            $data['mylast_comment_row']=StoryComment::find()
                ->select(['{{%story_comment}}.id','{{%story_comment}}.title','{{%story_comment_img}}.img_url'])
                ->leftJoin('{{%story_comment_img}}','{{%story_comment}}.comment_img_id={{%story_comment_img}}.id')
                ->andWhere(['=', '{{%story_comment}}.from_uid', $user->id])
                ->orderBy(['{{%story_comment}}.id'=>SORT_DESC])
                ->asArray()
                ->one();

            //我的旅行记录  travel_record story
            $data['travel_record']=TravelRecord::find()
                ->select(['{{%travel_record}}.story_id','{{%travel_record}}.history_chapters','{{%story}}.title','{{%story}}.record_pic'])
                ->leftJoin('{{%story}}','{{%travel_record}}.story_id={{%story}}.id')
                ->andWhere(['=', '{{%travel_record}}.user_id', $user->id])
                ->orderBy(['{{%travel_record}}.id'=>SORT_DESC])
                ->asArray()
                ->limit(2)
                ->all();

            //等待回复数
            $wait_comment_num=StoryComment::find()->where(['from_uid'=>$user->id,'status'=>0])->count();
            $wait_reply_num=StoryCommentReply::find()->where(['reply_to_uid'=>$user->id,'status'=>0])->count();
            $data['wait_reply_num']=$wait_comment_num+$wait_reply_num;

            //通知数
            $data['announce_num']=(int)StoryAnnouncePushLog::find()->where(['user_id'=>$user->id,'status'=>0])->count();

        }elseif($user_id>0){//如果没有Token，用户看别人
            //用户个人基本信息
            $data['user_profile']=Member::find()->select(['id','username','picture_url','nickname','signature','real_name_status','mobile'])->where(['id'=>$user_id])->asArray()->one();
            //Ta的评论
            $data['mylast_comment_row']=StoryComment::find()
                ->select(['{{%story_comment}}.id','{{%story_comment}}.title','{{%story_comment_img}}.img_url'])
                ->leftJoin('{{%story_comment_img}}','{{%story_comment}}.comment_img_id={{%story_comment_img}}.id')
                ->andWhere(['=', '{{%story_comment}}.from_uid', $user_id])
                ->orderBy(['{{%story_comment}}.id'=>SORT_DESC])
                ->asArray()
                ->one();

            //Ta的旅行记录

        }else{
            return parent::__response('参数错误!',(int)-2);
        }

        // var_dump(Yii::$app->user->getId());exit;// 获取用户id
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
            return parent::__response('获取失败',(int)-1);
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
            return parent::__response('获取失败',(int)-1);
        }

        //我的关注数组id
        $me_follower_arr=array();
        foreach($me_Follower_rows as $k=>$v){
            $me_follower_arr[]=(int)$v['id'];
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
            return parent::__response('获取失败',(int)-1);
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
            return parent::__response('获取失败',(int)-1);
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
        $picture_url=$model->upload();
        if($picture_url){
            //保存头像地址
            $member_model = Member::findOne($user_id);
            $member_model->picture_url=$picture_url;
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
     * 登录发送验证码
     * mobile string
     */
   public function actionSendSms(){
       if(!Yii::$app->request->isPost){//如果不是post请求
           return parent::__response('Request Error!',(int)-1);
       }
       if(!Yii::$app->request->post('mobile')){
           return parent::__response('手机号不能为空',(int)-2);
       }
       $mobile =Yii::$app->request->post('mobile')+0;
       $code=rand(100000,999999);

       if (!preg_match("/^[1][358][0-9]{9}$/", $mobile)) {
           return parent::__response('手机号格式错误，请重新输入！',(int)-2);
       }
       //查看最后一次发短信的时间，限制一个手机发短信的时间频率
       $last_send_time=SmsLog::find()->select(['created_at'])->where(['mobile'=>$mobile])->orderBy(['id'=>SORT_DESC])->scalar();
       if($last_send_time){
           if((time()-$last_send_time)<2*60){
               return parent::__response('短信已发出，请稍后在试!',(int)-1);
           }
       }
        //Yii::$app->request->getUserIP();exit;

       $send_sms=true;
       //发短信接口操作


       if($send_sms){
           $sms_model=new SmsLog();
           $sms_model->mobile=$mobile;
           $sms_model->code=$code;
           $sms_model->status=1;//发送成功
           $sms_model->send_type=1;//发送类别验证码
           $sms_model->created_at=time();

           //验证保存
           $isValid = $sms_model->validate();
           if ($isValid) {
               $r=$sms_model->save();
               if($r){
                   return parent::__response('发送成功,请在2分钟之内及时验证！',(int)0);
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

        if (!preg_match("/^[1][358][0-9]{9}$/", $mobile)) {
            return parent::__response('手机号格式错误，请重新输入！',(int)-2);
        }

        //先去看库里有没有此手机发的短信，且短信验证码没有过期
        //$sms_model=SmsLog::find()->andWhere(['mobile'=>$mobile,'status'=>1,'send_type'=>1])->andWhere(['in' , 'code' , [$code,123456]])->orderBy(['id'=>SORT_DESC])->one();
        $sms_model=SmsLog::find()->andWhere(['mobile'=>$mobile,'status'=>1,'send_type'=>1])->orderBy(['id'=>SORT_DESC])->one();//->andWhere(['in' , 'code' , [$code,123456]])
        if(!$sms_model){
            return parent::__response('手机号无效,请重新发送短信验证!',(int)-1);
        }

        //如果不是特例的验证码就要验证下
        if($code!=123456){
            if($sms_model->code!=$code){
                return parent::__response('验证码错误!',(int)-1);
            }
            if((time()-$sms_model->created_at)>2*60){//检验验证码是否过期
                return parent::__response('验证码已过期,请重新登录!',(int)-2);
            }
        }

        //查看用户是否有注册在用户表
        //$member_model=Member::find()->andWhere(['mobile'=>$mobile])->one();
        $member_model=Member::findByMobileUser($mobile);
        if(!$member_model){///如果没有，注册一个
            $user = new Member();
            $user->username = $mobile;
            $user->mobile = $mobile;
            $user->nickname = $mobile;
            $user->picture_url='/uploads/default/avatar.png';//头像
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
                return parent::__response('登录成功',0,['Token'=>$user->api_token]);
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
            return parent::__response('手机号不能为空',(int)-2);
        }
        $third_key =Yii::$app->request->post('third_key');
        $third_type =(int)Yii::$app->request->post('third_type');
        $nickname =Yii::$app->request->post('nickname');
        $headimgurl =Yii::$app->request->post('headimgurl');

        if(!in_array($third_type,[1,2,3])){//如果不是授权的范围内登录类型
            return parent::__response('参数错误，不在授权范围内',(int)-2);
        }

     //////////////判断一下是否是第一次授权登录，然后去自动注册一把/////////////
        $MemberAuths_model=MemberAuths::find()->andWhere(['third_key'=>$third_key,'third_type'=>$third_type])->one();//->andWhere(['in' , 'code' , [$code,123456]])
        if(!$MemberAuths_model){
            ///////首先没有记录是第一次登录，先去注册一个空账号
            $transaction=Yii::$app->db->beginTransaction();//开启事务
            $Member_model = new Member();
            $Member_model->username = $third_key;
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






        //登录一把返回Token
        $ThirdLoginForm_model = new ThirdLoginForm;
        $ThirdLoginForm_model->username=$username;
        if ($user = $ThirdLoginForm_model->username_login()) {
            if ($user instanceof IdentityInterface) {
                //return $user->api_token;
                return parent::__response('登录成功',0,['Token'=>$user->api_token]);
            } else {
                return $user->errors;
            }
        } else {
            return $ThirdLoginForm_model->errors;
        }

    }


	/**
	 * test
     * UploadForm[imageFile]
	 */
	public function actionTest ()
	{
//	    return [
//	        'xxxxxxxxxxxxx',
//	    ];
        $model = new UploadForm();

        if (Yii::$app->request->isPost) {
            //return file_get_contents('php://input');exit;
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // 文件上传成功
                return 'ok';
            }else{
                return '0';
            }
        }

	}

}