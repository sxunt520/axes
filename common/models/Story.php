<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story}}".
 *
 * @property string $id
 * @property string $title
 * @property string $intro
 * @property integer $type
 * @property string $cover_url
 * @property string $video_url
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $admin_id
 * @property integer $next_updated_at
 * @property integer $current_chapters
 * @property integer $total_chapters
 * @property integer $is_show
 */
class Story extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['type', 'created_at', 'updated_at', 'admin_id', 'current_chapters', 'total_chapters', 'is_show'], 'integer'],
            [['title','game_title'], 'string', 'max' => 50],
            [['cover_url', 'video_url','record_pic','game_title'], 'string', 'max' => 255],
            [['next_updated_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            ['created_at', 'default', 'value' => time()],
            ['admin_id', 'default', 'value' => Yii::$app->user->getId()],
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
            'intro' => '简介',
            'type' => '类型',
            'cover_url' => '封面地址',
            'video_url' => '视频地址',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'admin_id' => 'Admin ID',
            'next_updated_at' => '下次更时间',
            'current_chapters' => '当前章节',
            'total_chapters' => '总章节',
            'is_show' => '是否显示',
            'likes' => '点赞数',
            'views'=>'浏览数',
            'share_num'=>'分享数',
            'record_pic'=>'旅行记录图',
            'game_title'=>'游戏标题',
        ];
    }
}
