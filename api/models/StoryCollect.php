<?php

namespace api\models;

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
class StoryCollect extends \common\models\StoryCollect
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'user_id'], 'required'],
            [['story_id', 'user_id', 'created_at', 'status'], 'integer'],
            ['created_at', 'default','value' => time()],
        ];
    }

}
