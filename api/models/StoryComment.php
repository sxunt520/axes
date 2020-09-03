<<<<<<< HEAD
<?php

namespace api\models;

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
class StoryComment extends \common\models\StoryComment
{

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

}
=======
<?php

namespace api\models;

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
class StoryComment extends \common\models\StoryComment
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','story_id','comment_img_id','content','from_uid'],'required'],
            [['id','story_id', 'comment_type', 'from_uid', 'created_at','comment_img_id','heart_val','is_plot','likes','is_show','is_choiceness','is_top','views','share_num'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['content'], 'string'],
            ['created_at', 'default','value' => time()],
        ];
    }

}
>>>>>>> ebc4e1eb5bfbf77eee6f361efb99f1f57aa5a91c
