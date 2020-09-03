<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_like_log}}".
 *
 * @property string $id
 * @property string $reply_id
 * @property string $user_id
 * @property string $ip
 * @property string $create_at
 * @property integer $status
 */
class StoryCommentLikeReplyLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_comment_reply_like_log}}';
    }

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
            'reply_id' => 'Story ID',
            'user_id' => 'User ID',
            'ip' => 'Ip',
            'create_at' => 'Create At',
            'status' => 'Status',
        ];
    }
}
