<?php

namespace api\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\models\StoryRecommend;

/**
 * StoryRecommendSearch represents the model behind the search form about `common\models\StoryRecommend`.
 */
class StoryRecommendSearch extends StoryRecommend
{
    public $keyword='';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'story_id', 'created_at', 'is_show', 'orderby', 'likes', 'views', 'share_num'], 'integer'],
            [['title', 'cover_url', 'video_url'], 'safe'],
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
        $query = StoryRecommend::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,//一页多少条
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                    'orderby' => SORT_DESC,
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
            'type' => $this->type,
            'story_id' => $this->story_id,
            'created_at' => $this->created_at,
            'is_show' => $this->is_show,
            'orderby' => $this->orderby,
            'likes' => $this->likes,
            'views' => $this->views,
            'share_num' => $this->share_num,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'cover_url', $this->cover_url])
            ->andFilterWhere(['like', 'video_url', $this->video_url]);

        return $dataProvider;
    }


    public function search_story($params)
    {
        $query = self::find()
            ->select(['{{%story_recommend}}.id as recommend_id','{{%story_recommend}}.story_id','{{%story_recommend}}.title','{{%story_recommend}}.type','{{%story_recommend}}.cover_url','{{%story_recommend}}.video_url','{{%story_recommend}}.orderby','{{%story}}.game_title'])
            //->select('wujie_goods.*,wujie_member_verify.real_name')
            ->leftJoin('{{%story}}','{{%story_recommend}}.story_id={{%story}}.id')
        ;

        //
        $story_id_arr=array();//筛选story_id

        //匹配故事、游戏关键词
        if(!empty($params['keyword'])){
            $this->keyword=$params['keyword'];
            $query->orFilterWhere(['like', '{{%story_recommend}}.title', $this->keyword]);
            $query->orFilterWhere(['like', '{{%story}}.game_title', $this->keyword]);
        }

        if($story_id_arr){
            $query->andFilterWhere(['in' , 'story_id' ,$story_id_arr]);
        }

        return $query;
    }


}
