<?php

namespace common\models;

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
class SmsLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sms_log}}';
    }

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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'mobile' => 'Mobile',
            'code' => 'Code',
            'created_at' => 'Created At',
            'status' => 'Status',
            'send_type' => 'Send Type',
        ];
    }
}
