<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%shield}}".
 *
 * @property string $id
 * @property string $shield_from_uid
 * @property string $shield_to_uid
 * @property string $created_at
 * @property integer $status
 */
class Shield extends \common\models\Shield
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shield_from_uid', 'shield_to_uid', 'created_at', 'status'], 'integer']
        ];
    }

}
