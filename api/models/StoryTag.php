<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_tag}}".
 *
 * @property string $id
 * @property string $tag_name
 * @property integer $story_id
 */
class StoryTag extends \common\models\StoryTag
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id'], 'integer'],
            [['tag_name'], 'string', 'max' => 30]
        ];
    }

}
