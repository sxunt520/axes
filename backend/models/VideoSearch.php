<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\StoryVideo;

/**
 * VideoSearch represents the model behind the search form about `common\models\StoryVideo`.
 */
class VideoSearch extends StoryVideo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'story_id', 'video_type', 'is_show', 'views', 'likes', 'share_num', 'created_at'], 'integer'],
            [['title', 'video_url', 'video_cover'], 'safe'],
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
        $query = StoryVideo::find();

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
            'video_type' => $this->video_type,
            'is_show' => $this->is_show,
            'views' => $this->views,
            'likes' => $this->likes,
            'share_num' => $this->share_num,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'video_url', $this->video_url])
            ->andFilterWhere(['like', 'video_cover', $this->video_cover]);

        return $dataProvider;
    }
}
