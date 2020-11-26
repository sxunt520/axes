<?php

namespace api\modules\v1\controllers;

use api\models\Member;
use Yii;
use api\components\BaseController;
use api\models\Report;

class ReportController extends BaseController
{
    public function init(){
        parent::init();
    }

    public $modelClass = 'api\models\Report';

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
    public function actionAdd(){

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

}
