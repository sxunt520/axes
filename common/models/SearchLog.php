<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%search_log}}".
 *
 * @property string $id
 * @property string $keyword
 * @property string $user_id
 * @property string $created_at
 */
class SearchLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%search_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
            [['keyword'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'keyword' => '关键词',
            'user_id' => '用户id',
            'created_at' => '创建时间',
        ];
    }
}
