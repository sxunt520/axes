<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_announce_tag}}".
 *
 * @property integer $id
 * @property integer $announce_id
 * @property string $tage_name
 */
class StoryAnnounceTag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_announce_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['announce_id', 'tage_name'], 'required'],
            [['announce_id'], 'integer'],
            [['tag_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'announce_id' => 'Announce ID',
            'tage_name' => 'Tage Name',
        ];
    }
}
