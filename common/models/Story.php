<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

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
class Story extends \yii\db\ActiveRecord
{
    public $_video_url;

    private $_tagNames;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['game_title', 'intro','is_show'], 'required'],
            [['intro'], 'string'],
            [['type', 'created_at', 'updated_at', 'admin_id', 'current_chapters', 'total_chapters', 'is_show'], 'integer'],
            [['title','game_title'], 'string', 'max' => 50],
            [['cover_url', 'video_url','record_pic','game_title'], 'string', 'max' => 255],
            [['next_updated_at'], 'filter', 'filter' => 'strtotime', 'skipOnEmpty' => true],
            ['created_at', 'default', 'value' => time()],
            ['updated_at', 'default', 'value' => time()],
            ['is_show', 'default', 'value' => 0],
            ['admin_id', 'default', 'value' => Yii::$app->user->getId()],
            [['tagNames'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '游戏标题',
            'intro' => '游戏简介',
            'type' => '类型',
            'cover_url' => '封面地址',
            'video_url' => '视频地址',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'admin_id' => 'Admin ID',
            'next_updated_at' => '下次更时间',
            'current_chapters' => '当前章节',
            'total_chapters' => '总章节',
            'is_show' => '是否显示',
            'likes' => '点赞数',
            'views'=>'浏览数',
            'share_num'=>'分享数',
            'record_pic'=>'旅行记录图',
            'game_title'=>'游戏名称',
            'tagNames' => '标签',
        ];
    }

    public function attributeHints()
    {
        return [
            'tagNames' => '（空格分隔多个标签）'
        ];
    }

    //story 和story_tag 两个表实现一对多的关联绑定设置
    public function getTags()
    {
        return $this->hasMany(StoryTag::className(), ['story_id' => 'id']);
    }


    //渲染视图时get、自动获取tagNames字段内容的值，而这个值是通过上面关联绑定  用$this->tags获取相关绑定已存在tag值
    public function getTagNames()
    {
        $tags = $this->tags;
        if (!empty($tags)) {
            $tagNames = [];
            foreach($tags as $tag) {
                $tagNames[] = $tag->tag_name;
            }
            $tagNames = join(' ', $tagNames);
        } else {
            $tagNames = '';
        }
        return $tagNames;
    }

    //提交表单，is_post save 后，tagNames有设置，然后把值装入 _tagNames 待用~
    public function setTagNames($value)
    {
        $this->_tagNames = $value;
        return $this->_tagNames;
    }

    //提交后相关标签入库
    public function setTags()
    {
        // 先清除文章所有标签
        StoryTag::deleteAll(['story_id' => $this->id]);
        if (!empty($this->_tagNames)) {
            $tags = explode(' ', $this->_tagNames);
            foreach($tags as $tag) {
                $StoryTag_model=StoryTag::find()->andWhere(['tag_name'=>$tag,'story_id'=>$this->id])->one();
                if (empty($StoryTag_model)) {
                    $tagModel = new StoryTag();
                    $tagModel->tag_name = $tag;
                    $tagModel->story_id = $this->id;
                    $tagModel->save();
                }
            }

        }
    }

    /**
     *Time:2020/9/27 15:02
     *Author:始渲
     *Remark:获取故事列表
     * @params:
     */
    public static function getStoryRows()
    {
        // $list = Yii::$app->cache->get('categoryList');
        //if ($list === false) {
        $list = static::find()->select('id,game_title')->andWhere(['is_show'=>1])->asArray()->all();
        $list = ArrayHelper::map($list, 'id', 'game_title');
        //Yii::$app->cache->set('categoryList', $list);
        //}

        return $list;
    }


}
