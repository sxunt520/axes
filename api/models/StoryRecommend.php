<?php

namespace api\models;

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
class StoryRecommend extends \common\models\StoryRecommend
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['type', 'story_id', 'created_at', 'is_show', 'orderby', 'likes', 'views', 'share_num'], 'integer'],
            [['title', 'cover_url', 'video_url'], 'string', 'max' => 255]
        ];
    }

}
