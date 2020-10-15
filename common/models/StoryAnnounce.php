<?php

namespace common\models;

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
class StoryAnnounce extends \yii\db\ActiveRecord
{
    private $_tagNames;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%story_announce}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'user_id', 'is_show', 'views', 'likes', 'share_num', 'order_by', 'created_at', 'story_id'], 'integer'],
            [['title', 'content', 'story_id','is_show'], 'required'],
            [['title', 'content', 'pic_cover'], 'string', 'max' => 255],
            ['created_at', 'default', 'value' => time()],
            ['is_show', 'default', 'value' => 0],
            ['order_by', 'default', 'value' => 100],
            ['admin_id', 'default', 'value' => Yii::$app->user->id],
            ['user_id', 'default', 'value' => 12351],//前台官方旅人号id
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
            'admin_id' => '后台管理员ID',
            'user_id' => '官方旅人号id',
            'title' => '公告标题',
            'content' => '公告内容',
            'is_show' => '是否显示',
            'views' => '浏览数',
            'likes' => '点赞数',
            'share_num' => '分享数',
            'pic_cover' => '公告封面图',
            'order_by' => '排序值',
            'created_at' => '创建日期',
            'story_id' => '游戏ID',
            'tagNames' => '公告标签',
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
        return $this->hasMany(StoryAnnounceTag::className(), ['announce_id' => 'id']);
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
        StoryAnnounceTag::deleteAll(['announce_id' => $this->id]);
        if (!empty($this->_tagNames)) {
            $tags = explode(' ', $this->_tagNames);
            foreach($tags as $tag) {
                $StoryTag_model=StoryAnnounceTag::find()->andWhere(['tag_name'=>$tag,'announce_id'=>$this->id])->one();
                if (empty($StoryTag_model)) {
                    $tagModel = new StoryAnnounceTag();
                    $tagModel->tag_name = $tag;
                    $tagModel->announce_id = $this->id;
                    $tagModel->save();
                }
            }

        }
    }

}
