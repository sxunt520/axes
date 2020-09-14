<?php

namespace common\models;

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
class Follower extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%follower}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'follower_type', 'created_at', 'updated_at'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => 'From User ID',
            'to_user_id' => 'To User ID',
            'follower_type' => 'Follower Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
