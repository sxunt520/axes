<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_screen_comment}}".
 *
 * @property integer $id
 * @property string $story_id
 * @property string $text
 * @property string $from_uid
 * @property string $created_at
 * @property integer $is_show
 */
class StoryScreenComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_screen_comment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'from_uid', 'created_at', 'is_show'], 'integer'],
            [['text'], 'string', 'max' => 30]
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
            'text' => 'Text',
            'from_uid' => 'From Uid',
            'created_at' => 'Created At',
            'is_show' => 'Is Show',
        ];
    }
}
