<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_announce_like_log}}".
 *
 * @property integer $id
 * @property integer $story_id
 * @property integer $user_id
 * @property integer $ip
 * @property integer $create_at
 * @property integer $status
 */
class StoryAnnounceLikeLog extends \common\models\StoryAnnounceLikeLog
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['announce_id', 'user_id', 'ip', 'create_at', 'status'], 'integer']
        ];
    }




}
