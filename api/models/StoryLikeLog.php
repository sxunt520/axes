<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_like_log}}".
 *
 * @property integer $id
 * @property integer $story_id
 * @property integer $user_id
 * @property string $ip
 * @property integer $create_at
 * @property integer $status
 */
class StoryLikeLog extends \common\models\StoryLikeLog
{
    public $error;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'user_id', 'create_at', 'status'], 'integer'],
            //[['ip'], 'string', 'max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_id' => 'Story ID',
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
    public function apiLike($story_id,$user_id){
        $content=Story::find()->where(['id'=>$story_id])->select(['id','likes'])->asArray()->one();
        if (empty($content)){
            $this->error='故事不存在！';
            return false;
        }
        //$r = $this->findOne(['story_id'=>$story_id,'ip'=>ip2long(Yii::$app->request->getUserIP())],'create_at desc');
        //$r=self::find()->where(['story_id' => $story_id,'user_id' => $user_id,'ip'=>ip2long(Yii::$app->request->getUserIP())])->orderBy(['create_at' => SORT_DESC])->one();
        $r=self::find()->where(['story_id' => $story_id,'user_id' => $user_id])->orderBy(['create_at' => SORT_DESC])->one();
        //var_dump($r->create_at) ;exit;
        //$_time=time()-($r->create_at);
        //var_dump($v);exit;

        if ($r && time()-($r->create_at) < Yii::$app->params['user.liketime']){
            $this->error='两次点赞间隔不能低于'.Yii::$app->params['user.liketime'].'秒';
            return false;
        }else{
            $transaction=Yii::$app->db->beginTransaction();
            try{
                $this->story_id = $story_id;
                $this->ip =ip2long(Yii::$app->request->getUserIP());
                $this->user_id = $user_id;
                $this->status = 1;
                $this->create_at = time();
                $r=$this->save();//保存日志 过滤器还保存了一个ip
                //var_dump($this);exit;
                //锁定行
                $sql="select likes from {{%story}} where id={$story_id} for update";
                $data=Yii::$app->db->createCommand($sql)->query()->read();
                $sql="update {{%story}} set likes=likes+1 where id={$story_id}";
                Yii::$app->db->createCommand($sql)->execute();
                $transaction->commit();
                Yii::$app->cache->set('story_id:'.$story_id,$data['likes']+1);
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