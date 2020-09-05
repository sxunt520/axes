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

    /**
     * 真实浏览量
     */
    public static function getTrueViews($id)
    {
        $story_model=SELF::findOne($id);
        return $story_model->views + \Yii::$app->cache->get('story_comment:views:' . $story_model->id);
    }

    /**
     * 增加故事浏览量
     */
    public static function addView($id)
    {
        $story_model=SELF::findOne($id);
        $cache = \Yii::$app->cache;
        $key = 'story_comment:views:'.$story_model->id;
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
