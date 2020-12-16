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
            //[['title', 'video_url'], 'required'],
            [['title', 'video_url', 'video_cover','content'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '视频ID',
            'story_id' => '故事id',
            'title' => '视频标题',
            'video_url' => '视频地址',
            'video_cover' => '视频封面图',
            'video_type' => '视频类别',
            'is_show' => '是否显示',
            'views' => '浏览量',
            'likes' => '点赞数',
            'share_num' => '分享数',
            'created_at' => '创建时间',
        ];
    }
}
