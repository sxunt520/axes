<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_video_topic}}".
 *
 * @property string $id
 * @property string $story_id
 * @property string $topic_title
 * @property string $topic_cover
 * @property string $content
 * @property integer $is_show
 * @property string $created_at
 */
class StoryVideoTopic extends \yii\db\ActiveRecord
{
    public $_topic_video_url;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_video_topic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id','topic_title','is_show'], 'required'],
            [['story_id', 'is_show', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['topic_title'], 'string', 'max' => 150],
            [['topic_cover','topic_video_url'], 'string', 'max' => 255],
            ['created_at', 'default', 'value' => time()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_id' => '故事游戏id',
            'topic_title' => '主题标题',
            'topic_cover' => '主题封面',
            'content' => '主题内容',
            'is_show' => '是否显示',
            'created_at' => '创建时间',
            'topic_video_url'=>'主题视频',
        ];
    }
}
