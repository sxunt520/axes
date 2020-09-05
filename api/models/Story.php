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

    /**
     * 真实浏览量
     */
    public static function getTrueViews($id)
    {
        $story_model=SELF::findOne($id);
        return $story_model->views + \Yii::$app->cache->get('story:views:' . $story_model->id);
    }

    /**
     * 增加故事浏览量
     */
    public static function addView($id)
    {
        $story_model=SELF::findOne($id);
        $cache = \Yii::$app->cache;
        $key = 'story:views:'.$story_model->id;
        $views = $cache->get($key);
        if ($views !== false) {//echo $views;exit;
            if ($views >= 10) {//超过10次更新数据库
                $story_model->views = $story_model->views + $views + 1;
                $story_model->save(false);
                $cache->delete($key);
            } else {
                $cache->set($key, ++$views);
            }
        } else {
            $cache->set($key, 1);
        }
    }

}
