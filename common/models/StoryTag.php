<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_tag}}".
 *
 * @property string $id
 * @property string $tag_name
 * @property integer $story_id
 */
class StoryTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id'], 'integer'],
            [['tag_name'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_name' => 'Tag Name',
            'story_id' => 'Story ID',
        ];
    }
}
