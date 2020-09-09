<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%sms_log}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $mobile
 * @property integer $code
 * @property string $created_at
 * @property integer $status
 * @property integer $send_type
 */
class SmsLog extends  \common\models\SmsLog
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'mobile', 'code', 'created_at', 'status', 'send_type'], 'integer'],
            [['mobile'], 'required']
        ];
    }

}
