<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%follower}}".
 *
 * @property integer $id
 * @property integer $from_user_id
 * @property integer $to_user_id
 * @property integer $follower_type
 * @property integer $created_at
 * @property integer $updated_at
 */
class Follower extends \common\models\Follower
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'follower_type', 'created_at', 'updated_at'], 'integer']
        ];
    }


}
