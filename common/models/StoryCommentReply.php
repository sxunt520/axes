<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%story_comment_reply}}".
 *
 * @property string $id
 * @property integer $comment_id
 * @property integer $reply_type
 * @property string $reply_content
 * @property integer $reply_from_uid
 * @property integer $reply_to_uid
 * @property integer $reply_at
 * @property integer $status
 * @property integer $is_show
 * @property string $likes
 */
class StoryCommentReply extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_comment_reply}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id', 'reply_type', 'reply_from_uid', 'reply_to_uid', 'reply_at', 'status', 'is_show', 'likes'], 'integer'],
            [['reply_content'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'comment_id' => 'Comment ID',
            'reply_type' => 'Reply Type',
            'reply_content' => 'Reply Content',
            'reply_from_uid' => 'Reply From Uid',
            'reply_to_uid' => 'Reply To Uid',
            'reply_at' => 'Reply At',
            'status' => 'Status',
            'is_show' => 'Is Show',
            'likes' => 'Likes',
        ];
    }
}
