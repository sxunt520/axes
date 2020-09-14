<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%story_announce_push_log}}".
 *
 * @property integer $id
 * @property integer $announce_id
 * @property integer $user_id
 * @property integer $status
 * @property integer $created_at
 * @property integer $admin_id
 * @property integer $update_at
 */
class StoryAnnouncePushLog extends \common\models\StoryAnnouncePushLog
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['announce_id', 'user_id', 'status', 'created_at', 'admin_id', 'update_at'], 'integer']
        ];
    }

}
