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
            [['type', 'created_at', 'updated_at', 'admin_id', 'next_updated_at', 'current_chapters', 'total_chapters', 'is_show'], 'integer'],
            [['title','game_title'], 'string', 'max' => 50],
            [['cover_url', 'video_url','record_pic'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'intro' => 'Intro',
            'type' => 'Type',
            'cover_url' => '封面地址',
            'video_url' => '视频地址',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'admin_id' => 'Admin ID',
            'next_updated_at' => '下次更新时间',
            'current_chapters' => '当前章节',
            'total_chapters' => '总章节',
            'is_show' => '是否显示',
        ];
    }
}
