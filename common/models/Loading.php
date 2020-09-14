<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%loading}}".
 *
 * @property integer $id
 * @property string $img_url
 * @property integer $type
 * @property integer $created_at
 * @property integer $order_by
 * @property integer $is_show
 * @property string $title
 */
class Loading extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%loading}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['img_url'], 'required'],
            [['type', 'created_at', 'order_by', 'is_show'], 'integer'],
            [['img_url', 'title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'img_url' => 'Img Url',
            'type' => 'Type',
            'created_at' => 'Created At',
            'order_by' => 'Order By',
            'is_show' => 'Is Show',
            'title' => 'Title',
        ];
    }
}
