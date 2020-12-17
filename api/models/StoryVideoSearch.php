<?php

namespace api\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\models\StoryVideo;

/**
 * StoryRecommendSearch represents the model behind the search form about `common\models\StoryRecommend`.
 */
class StoryVideoSearch extends StoryVideo
{
    public $keyword='';


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search_video($params)
    {
        $query = self::find()
            ->select(['{{%story_video}}.id as video_id','{{%story_video}}.story_id','{{%story_video}}.title','{{%story_video}}.video_url','{{%story_video}}.video_cover','{{%story_video}}.content','{{%story}}.game_title'])
            //->select('wujie_goods.*,wujie_member_verify.real_name')
            ->leftJoin('{{%story}}','{{%story_video}}.story_id={{%story}}.id')
        ;

        //
        $story_id_arr=array();//筛选story_id

        //匹配视频title content、游戏关键词
        if(!empty($params['keyword'])){
            $this->keyword=$params['keyword'];
            $query->orFilterWhere(['like', '{{%story_video}}.title', $this->keyword]);
            $query->orFilterWhere(['like', '{{%story_video}}.content', $this->keyword]);
            $query->orFilterWhere(['like', '{{%story}}.game_title', $this->keyword]);
        }

        if($story_id_arr){
            $query->andFilterWhere(['in' , 'story_id' ,$story_id_arr]);
        }

        return $query;
    }


}
