<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_announce}}".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property integer $user_id
 * @property string $title
 * @property string $content
 * @property integer $is_show
 * @property integer $views
 * @property integer $likes
 * @property integer $share_num
 * @property string $pic_cover
 * @property integer $order_by
 * @property integer $created_at
 * @property integer $story_id
 */
class StoryAnnounce extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_announce}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'user_id', 'is_show', 'views', 'likes', 'share_num', 'order_by', 'created_at', 'story_id'], 'integer'],
            [['title', 'content', 'story_id'], 'required'],
            [['title', 'content', 'pic_cover'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'admin_id' => 'Admin ID',
            'user_id' => 'User ID',
            'title' => 'Title',
            'content' => 'Content',
            'is_show' => 'Is Show',
            'views' => 'Views',
            'likes' => 'Likes',
            'share_num' => 'Share Num',
            'pic_cover' => 'Pic Cover',
            'order_by' => 'Order By',
            'created_at' => 'Created At',
            'story_id' => 'Story ID',
        ];
    }
}
