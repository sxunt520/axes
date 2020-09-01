<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_img}}".
 *
 * @property string $id
 * @property integer $story_id
 * @property string $img_url
 * @property string $img_text
 * @property integer $updated_at
 */
class StoryImg extends \common\models\StoryImg
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'updated_at'], 'integer'],
            [['img_url', 'img_text'], 'string', 'max' => 255]
        ];
    }

}
