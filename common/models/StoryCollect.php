<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_collect}}".
 *
 * @property integer $id
 * @property integer $story_id
 * @property integer $user_id
 * @property integer $created_at
 * @property integer $status
 */
class StoryCollect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_collect}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'user_id', 'created_at'], 'required'],
            [['story_id', 'user_id', 'created_at', 'status'], 'integer']
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
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }
}
