<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%app_config}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $desc
 * @property integer $created_at
 * @property integer $updated_at
 */
class AppConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'desc', 'created_at', 'updated_at'], 'required'],
            [['value'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['desc'], 'string', 'max' => 255],
           // ['created_at', 'default', 'value' => time()],
            //['updated_at', 'default', 'value' => time()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
            'desc' => 'Desc',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

}
