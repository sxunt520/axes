<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_like_log}}".
 *
 * @property integer $id
 * @property integer $reply_id
 * @property integer $user_id
 * @property integer $ip
 * @property integer $create_at
 * @property integer $status
 */
class StoryCommentReplyLikeLog extends \common\models\StoryCommentReplyLikeLog
{
    public $error;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reply_id', 'user_id', 'ip', 'create_at', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reply_id' => 'reply_id',
            'user_id' => 'User ID',
            'ip' => 'Ip',
            'create_at' => 'Create At',
            'status' => 'Status',
        ];
    }

    //数据库里更新点赞数，事务控制 成功点赞数存入缓存 Like_log
    public function apiLike($reply_id,$user_id){
        $content=StoryCommentReply::find()->where(['id'=>$reply_id])->select(['id','likes'])->asArray()->one();
        if (empty($content)){
            $this->error='回复评论不存在！';
            return false;
        }

        $r=self::find()->where(['reply_id' => $reply_id,'ip'=>ip2long(Yii::$app->request->getUserIP())])->orderBy(['create_at' => SORT_DESC])->one();

        if ($r && time()-($r->create_at) < Yii::$app->params['user.liketime']){
            $this->error='两次点赞间隔不能低于'.Yii::$app->params['user.liketime'].'秒';
            return false;
        }else{
            $transaction=Yii::$app->db->beginTransaction();
            try{
                $this->reply_id = $reply_id;
                $this->ip =ip2long(Yii::$app->request->getUserIP());
                $this->user_id = $user_id;
                $this->status = 1;
                $this->create_at = time();
                $r=$this->save();//保存日志
                
                //锁定行
                $sql="select likes from {{%story_comment_reply}} where id={$reply_id} for update";
                $data=Yii::$app->db->createCommand($sql)->query()->read();
                $sql="update {{%story_comment_reply}} set likes=likes+1 where id={$reply_id}";
                Yii::$app->db->createCommand($sql)->execute();
                $transaction->commit();
                Yii::$app->cache->set('reply_id:'.$reply_id,$data['likes']+1);
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
