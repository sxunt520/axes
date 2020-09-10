<?php

namespace common\models;

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
class MemberAuths extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_auths}}';
    }

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

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'third_key' => 'Third Key',
            'third_type' => 'Third Type',
            'created_at' => 'Created At',
        ];
    }
}
