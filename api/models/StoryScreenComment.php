<?php

namespace api\models;

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
class StoryScreenComment  extends \common\models\StoryScreenComment
{
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

}
