<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_like_log}}".
 *
 * @property string $id
 * @property string $comment_id
 * @property string $user_id
 * @property string $ip
 * @property string $create_at
 * @property integer $status
 */
class StoryCommentLikeLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_comment_like_log}}';
    }

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
}
