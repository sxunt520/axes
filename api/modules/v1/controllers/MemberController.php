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
	 * test
	 */
	public function actionTest ()
	{
	    return [
	        'xxxxxxxxxxxxx',
	    ];
	}

}