<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StoryComment;

/**
 * StoryCommentSearch represents the model behind the search form about `common\models\StoryComment`.
 */
class StoryCommentSearch extends StoryComment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'story_id', 'comment_type', 'from_uid', 'created_at', 'comment_img_id', 'heart_val', 'is_plot', 'likes', 'is_show', 'is_choiceness', 'is_top', 'views', 'share_num', 'status', 'update_at'], 'integer'],
            [['content', 'title'], 'safe'],
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
        $query = StoryComment::find();

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
            'comment_type' => $this->comment_type,
            'from_uid' => $this->from_uid,
            'created_at' => $this->created_at,
            'comment_img_id' => $this->comment_img_id,
            'heart_val' => $this->heart_val,
            'is_plot' => $this->is_plot,
            'likes' => $this->likes,
            'is_show' => $this->is_show,
            'is_choiceness' => $this->is_choiceness,
            'is_top' => $this->is_top,
            'views' => $this->views,
            'share_num' => $this->share_num,
            'status' => $this->status,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }

    //下拉筛选控制
    public static function dropDown ($column, $value = null)
    {
        $dropDownList = [
            "type"=> [
                "1"=>"图片",
                "2"=>"视频",
            ],
            "is_show"=> [
                "0"=>"否",
                "1"=>"是",
            ],
            "is_show_html"=> [
                "0"=>'<span class="label label-danger"  style="margin-left:22px;"><i class="fa fa-times"></i></span>',
                "1"=>'<span class="label label-success"  style="margin-left:22px;"><i class="fa fa-check"></i></span>',
            ],

            "is_plot"=> [
                "0"=>"否",
                "1"=>"是",
            ],
            "is_plot_html"=> [
                "0"=>'<span class="label label-danger"  style="margin-left:22px;"><i class="fa fa-times"></i></span>',
                "1"=>'<span class="label label-success"  style="margin-left:22px;"><i class="fa fa-check"></i></span>',
            ],

            "is_choiceness"=> [
                "0"=>"否",
                "1"=>"是",
            ],
            "is_choiceness_html"=> [
                "0"=>'<span class="label label-danger"  style="margin-left:22px;"><i class="fa fa-times"></i></span>',
                "1"=>'<span class="label label-success"  style="margin-left:22px;"><i class="fa fa-check"></i></span>',
            ],

            "is_top"=> [
                "0"=>"否",
                "1"=>"是",
            ],
            "is_top_html"=> [
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
