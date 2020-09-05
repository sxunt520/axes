<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_like_log}}".
 *
 * @property integer $id
 * @property integer $comment_id
 * @property integer $user_id
 * @property integer $ip
 * @property integer $create_at
 * @property integer $status
 */
class StoryCommentLikeLog extends \common\models\StoryCommentLikeLog
{
    public $error;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'user_id', 'ip', 'create_at', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment_id' => 'comment_id',
            'user_id' => 'User ID',
            'ip' => 'Ip',
            'create_at' => 'Create At',
            'status' => 'Status',
        ];
    }

//    function beforeSave()
//    {
//        $this->ip = Yii::$app->request->getUserIP();
//        return true;
//    }

    //数据库里更新点赞数，事务控制 成功点赞数存入缓存 Like_log
    public function apiLike($comment_id,$user_id){
        $content=StoryComment::find()->where(['id'=>$comment_id])->select(['id','likes'])->asArray()->one();
        if (empty($content)){
            $this->error='评论内容不存在！';
            return false;
        }
        $r=self::find()->where(['comment_id' => $comment_id,'ip'=>ip2long(Yii::$app->request->getUserIP())])->orderBy(['create_at' => SORT_DESC])->one();

        if ($r && time()-($r->create_at) < 10){
            $this->error='两次点赞间隔不能低于10秒';
            return false;
        }else{
            $transaction=Yii::$app->db->beginTransaction();
            try{
                $this->comment_id = $comment_id;
                $this->ip =ip2long(Yii::$app->request->getUserIP());
                $this->user_id = $user_id;
                $this->status = 1;
                $this->create_at = time();
                $r=$this->save();//保存日志

                //锁定行
                $sql="select likes from {{%story_comment}} where id={$comment_id} for update";
                $data=Yii::$app->db->createCommand($sql)->query()->read();
                //更新一个赞，+10热度
                $sql="update {{%story_comment}} set likes=likes+1,heart_val=heart_val+10 where id={$comment_id}";
                Yii::$app->db->createCommand($sql)->execute();

                $transaction->commit();
                Yii::$app->cache->set('comment_id:'.$comment_id,$data['likes']+1);
            }catch (Exception $e){
                Yii::error($e->getMessage());
                $this->error=json_encode($e->getMessage());
                $transaction->rollBack();
                return false;
            }

        }
        return true;
    }

}
