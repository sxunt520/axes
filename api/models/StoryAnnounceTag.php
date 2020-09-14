<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_announce_tag}}".
 *
 * @property integer $id
 * @property integer $announce_id
 * @property string $tage_name
 */
class StoryAnnounceTag extends \common\models\StoryAnnounceTag
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['announce_id', 'tage_name'], 'required'],
            [['announce_id'], 'integer'],
            [['tag_name'], 'string', 'max' => 255]
        ];
    }


}
