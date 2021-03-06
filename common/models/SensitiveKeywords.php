<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%sensitive_keywords}}".
 *
 * @property string $id
 * @property string $word
 */
class SensitiveKeywords extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sensitive_keywords}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'], 'string'],
            [['desc'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word' => '敏感词组',
            'desc' => '描述',
        ];
    }
}
