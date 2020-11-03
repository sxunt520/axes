<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_comment}}".
 *
 * @property string $id
 * @property integer $story_id
 * @property integer $comment_type
 * @property string $content
 * @property integer $from_uid
 * @property integer $created_at
 * @property integer $update_at
 * @property string $comment_img
 */
class StoryComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_comment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['story_id', 'comment_type', 'from_uid', 'created_at', 'update_at','comment_img_id','heart_val','is_plot','likes','is_show','is_choiceness','is_top','views','share_num'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'story_id' => '游戏id',
            'comment_type' => '评论类型',
            'content' => '评论内容',
            'from_uid' => '评论用户旅人号id',
            'created_at' => '评论时间',
            'comment_img' => '评论图',
            'update_at' => '更新时间',

           'comment_img_id' => '评论图id',
            'heart_val' => '热度值',
            'is_plot' => '是否包含剧透',
            'likes' => '点赞数',
            'is_show' => '是否删除隐藏',
            'is_choiceness' => '是否是精选',
            'is_top' => '是否置顶',
            'views' => '浏览数',
            'share_num' => '分享数',
            'title' => '评论标题',
            'status' => '状态',
        ];
    }
}
