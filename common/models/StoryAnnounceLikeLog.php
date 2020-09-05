<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_announce_like_log}}".
 *
 * @property integer $id
 * @property integer $story_id
 * @property integer $user_id
 * @property integer $ip
 * @property integer $create_at
 * @property integer $status
 */
class StoryAnnounceLikeLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_announce_like_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['announce_id', 'user_id', 'ip', 'create_at', 'status'], 'integer']
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
}
