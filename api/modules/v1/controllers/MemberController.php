<?php

namespace api\modules\v1\controllers;

use Yii;
use api\components\BaseController;
use yii\web\IdentityInterface;
use api\models\Member;
use api\models\LoginForm;
use api\models\StoryComment;
use api\models\StoryCommentImg;
use api\models\StoryCommentReply;
use api\models\StoryAnnouncePushLog;
use api\models\Follower;

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

            //我的旅行记录

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
	 * test
	 */
	public function actionTest ()
	{
	    return [
	        'xxxxxxxxxxxxx',
	    ];
	}

}