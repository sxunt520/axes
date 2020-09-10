<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%member_auths}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $third_key
 * @property integer $third_type
 * @property string $created_at
 */
class MemberAuths extends \common\models\MemberAuths
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'third_type', 'created_at'], 'integer'],
            [['third_key'], 'string', 'max' => 255]
        ];
    }

}
