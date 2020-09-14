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
            [['story_id', 'comment_type', 'from_uid', 'created_at','comment_img_id','heart_val','is_plot','likes','is_show','is_choiceness','is_top','views','share_num'], 'integer'],
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
            'story_id' => 'Story ID',
            'comment_type' => 'Comment Type',
            'content' => 'Content',
            'from_uid' => 'From Uid',
            'created_at' => 'Created At',
            'comment_img' => 'Comment Img',
        ];
    }
}
