<?php

namespace api\models;

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
class StoryAnnounce extends  \common\models\StoryAnnounce
{

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
     * 真实浏览量
     */
    public static function getTrueViews($announce_id)
    {
        $story_announce_model=SELF::findOne($announce_id);
        return $story_announce_model->views + \Yii::$app->cache->get('story_announce:views:' . $story_announce_model->id);
    }

    /**
     * 增加故事公告浏览量
     */
    public static function addView($announce_id)
    {
        $story_announce_model=SELF::findOne($announce_id);
        $cache = \Yii::$app->cache;
        $key = 'story_announce:views:'.$story_announce_model->id;
        $views = $cache->get($key);
        if ($views !== false) {//echo $views;exit;
            if ($views >= 10) {//超过10次更新数据库
                $story_announce_model->views = $story_announce_model->views + $views + 1;
                $story_announce_model->save(false);
                $cache->delete($key);
            } else {
                $cache->set($key, ++$views);
            }
        } else {
            $cache->set($key, 1);
        }
    }


}
