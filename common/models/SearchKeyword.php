<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%search_keyword}}".
 *
 * @property string $id
 * @property string $keyword
 * @property string $total_num
 */
class SearchKeyword extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%search_keyword}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_num'], 'integer'],
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
            'keyword' => 'Keyword',
            'total_num' => 'Total Num',
        ];
    }
}
