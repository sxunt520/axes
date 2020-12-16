<?php

namespace api\models;

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
class StoryVideoTopic extends \common\models\StoryVideoTopic
{

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

}
