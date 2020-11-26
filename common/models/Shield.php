<?php

namespace common\models;

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
class Shield extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shield}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shield_from_uid', 'shield_to_uid', 'created_at', 'status'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shield_from_uid' => 'Shield From Uid',
            'shield_to_uid' => 'Shield To Uid',
            'created_at' => 'Created At',
            'status' => 'Status',
        ];
    }
}
