<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_recommend}}".
 *
 * @property string $id
 * @property string $title
 * @property integer $type
 * @property string $cover_url
 * @property string $video_url
 * @property string $story_id
 * @property string $created_at
 * @property integer $is_show
 * @property string $orderby
 * @property string $likes
 * @property string $views
 * @property string $share_num
 */
class StoryRecommend extends \yii\db\ActiveRecord
{
    public $_video_url;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_recommend}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','story_id','type'], 'required'],
            [['type', 'story_id', 'created_at', 'is_show', 'orderby', 'likes', 'views', 'share_num'], 'integer'],
            [['title', 'cover_url', 'video_url'], 'string', 'max' => 255],
            ['created_at', 'default', 'value' => time()],
            ['orderby', 'default', 'value' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'type' => '类型',
            'cover_url' => '推荐封面',
            'video_url' => '推荐视频',
            'story_id' => '所属故事',
            'created_at' => '创建时间',
            'is_show' => '是否显示',
            'orderby' => '排序值',
            'likes' => '点赞数',
            'views' => '浏览数',
            'share_num' => '分享数',
        ];
    }
}
