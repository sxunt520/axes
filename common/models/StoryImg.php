<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_img}}".
 *
 * @property string $id
 * @property integer $story_id
 * @property string $img_url
 * @property string $img_text
 * @property integer $updated_at
 */
class StoryImg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_img}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'updated_at'], 'integer'],
            [['img_url', 'img_text'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_id' => 'Story ID',
            'img_url' => 'Img Url',
            'img_text' => 'Img Text',
            'updated_at' => 'Updated At',
        ];
    }
}
