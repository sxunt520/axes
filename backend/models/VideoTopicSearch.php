<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StoryVideoTopic;

/**
 * VideoTopicSearch represents the model behind the search form about `common\models\StoryVideoTopic`.
 */
class VideoTopicSearch extends StoryVideoTopic
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'story_id', 'is_show', 'created_at'], 'integer'],
            [['topic_title', 'topic_cover', 'content'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = StoryVideoTopic::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'story_id' => $this->story_id,
            'is_show' => $this->is_show,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'topic_title', $this->topic_title])
            ->andFilterWhere(['like', 'topic_cover', $this->topic_cover])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }


    //下拉筛选控制
    public static function dropDown ($column, $value = null)
    {
        $dropDownList = [
            "is_show"=> [
                "0"=>"否",
                "1"=>"是",
            ],
            "is_show_html"=> [
                "0"=>'<span class="label label-danger"  style="margin-left:22px;"><i class="fa fa-times"></i></span>',
                "1"=>'<span class="label label-success"  style="margin-left:22px;"><i class="fa fa-check"></i></span>',
            ],
            //有新的字段要实现下拉规则，可像上面这样进行添加
            // ......
        ];
        //根据具体值显示对应的值
        if ($value !== null)
            return array_key_exists($column, $dropDownList) ? $dropDownList[$column][$value] : false;
        //返回关联数组，用户下拉的filter实现
        else
            return array_key_exists($column, $dropDownList) ? $dropDownList[$column] : false;
    }


}
