<?php

namespace api\models;

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
class AppConfig  extends \common\models\AppConfig
{

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
            [['desc'], 'string', 'max' => 255]
        ];
    }

}
