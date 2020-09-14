<?php

namespace api\models;

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
 * @property integer $parent_reply_id
 */
class StoryCommentReply extends \common\models\StoryCommentReply
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['comment_id','reply_type','reply_from_uid','reply_to_uid'],'required'],
            [['comment_id', 'reply_type', 'reply_from_uid', 'reply_to_uid', 'reply_at', 'status', 'is_show', 'likes','parent_reply_id'], 'integer'],
            [['reply_content'], 'string'],
            ['reply_at', 'default','value' => time()],
            ['status', 'default','value' =>0],//;// 状态 0未读 1已读 2已回
            ['is_show', 'default','value' =>1],//是否显示
        ];
    }

}
