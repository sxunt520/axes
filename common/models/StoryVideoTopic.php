<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_video_topic}}".
 *
 * @property string $id
 * @property string $story_id
 * @property string $topic_title
 * @property string $topic_cover
 * @property string $content
 * @property integer $is_show
 * @property string $created_at
 */
class StoryVideoTopic extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_video_topic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'is_show', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['topic_title'], 'string', 'max' => 150],
            [['topic_cover'], 'string', 'max' => 255]
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
            'topic_title' => 'Topic Title',
            'topic_cover' => 'Topic Cover',
            'content' => 'Content',
            'is_show' => 'Is Show',
            'created_at' => 'Created At',
        ];
    }
}
