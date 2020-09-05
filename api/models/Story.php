<?php

namespace api\models;

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
class Story extends \common\models\Story
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['type', 'created_at', 'updated_at', 'admin_id', 'next_updated_at', 'current_chapters', 'total_chapters', 'is_show','views','share_num'], 'integer'],
            [['title'], 'string', 'max' => 50],
            [['cover_url', 'video_url'], 'string', 'max' => 255]
        ];
    }

}
