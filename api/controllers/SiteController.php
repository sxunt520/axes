<?php
namespace api\controllers;

use Yii;
use yii\web\Controller;


/**
 * Site controller
 */
class SiteController extends Controller
{
    //全局异常处理类
    public function actions()
    {
        return [
            'error' => [
                'class' => 'api\components\ExceptionHandler',
            ],
        ];
    }
    
    public function actionIndex()
    {
        return '访问无效';
    }
    
    public function actionTest()
    {
        var_dump($_POST);exit;
        //throw new \yii\web\UnauthorizedHttpException("这是一个测试接口");

        //多少人赞过 仅显示最开始点赞的6位用户
//        $sql_num="select count(*) as likes_num from (select * from {{%story_comment_like_log}} where comment_id=2 group by user_id) as like_log_num;";
//        $likes_num=Yii::$app->db->createCommand($sql_num)->queryScalar();
//        $sql='select like_log.comment_id,like_log.user_id,like_log.create_at,s_member.username,s_member.picture_url from (select * from {{%story_comment_like_log}} where comment_id=1 group by user_id) as like_log INNER JOIN {{%member}} on like_log.user_id=s_member.id ORDER BY like_log.create_at ASC limit 2';
//        $r=Yii::$app->db->createCommand($sql)->queryAll();
        //var_dump($likes_num);

    }

}
