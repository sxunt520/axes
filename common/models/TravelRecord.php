<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%travel_record}}".
 *
 * @property string $id
 * @property integer $story_id
 * @property integer $user_id
 * @property integer $create_at
 * @property string $update_at
 * @property string $history_chapters
 */
class TravelRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%travel_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'user_id', 'create_at', 'update_at', 'history_chapters'], 'integer']
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
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'history_chapters' => 'History Chapters',
        ];
    }
}
