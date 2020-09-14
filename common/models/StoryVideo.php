<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_video}}".
 *
 * @property integer $id
 * @property integer $story_id
 * @property string $title
 * @property string $video_url
 * @property string $video_cover
 * @property integer $video_type
 * @property integer $is_show
 * @property integer $views
 * @property integer $likes
 * @property integer $share_num
 * @property integer $created_at
 */
class StoryVideo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_video}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'video_type', 'is_show', 'views', 'likes', 'share_num', 'created_at'], 'integer'],
            [['title', 'video_url', 'video_cover'], 'required'],
            [['title', 'video_url', 'video_cover'], 'string', 'max' => 255]
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
            'title' => 'Title',
            'video_url' => 'Video Url',
            'video_cover' => 'Video Cover',
            'video_type' => 'Video Type',
            'is_show' => 'Is Show',
            'views' => 'Views',
            'likes' => 'Likes',
            'share_num' => 'Share Num',
            'created_at' => 'Created At',
        ];
    }
}
