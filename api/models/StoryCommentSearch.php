<?php

namespace api\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use api\models\StoryComment;

/**
 * StoryRecommendSearch represents the model behind the search form about `common\models\StoryRecommend`.
 */
class StoryCommentSearch extends StoryComment
{
    public $keyword='';
    public $is_show=1;


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search_comment($params)
    {
        $query = self::find()
            ->select(['{{%story_comment}}.id as comment_id','{{%story_comment}}.story_id','{{%story_comment}}.title','{{%story_comment}}.content','{{%story_comment}}.from_uid','{{%story_comment}}.from_uid','{{%story_comment}}.likes','{{%member}}.username','{{%member}}.nickname','{{%member}}.picture_url','{{%story}}.game_title'])
            ->leftJoin('{{%member}}','{{%story_comment}}.from_uid={{%member}}.id')
            ->leftJoin('{{%story}}','{{%story_comment}}.story_id={{%story}}.id')
        ;

        //
        $story_id_arr=array();//筛选story_id

        $query->andFilterWhere(['=' , '{{%story_comment}}.comment_type' ,0]);//0评论故事，1评论公告
        $query->andFilterWhere(['=' , '{{%story_comment}}.is_show' ,1]);//是否显示1是0否

        //匹配视频title content、游戏关键词
        if(!empty($params['keyword'])){
            $this->keyword=$params['keyword'];
            //$query->orFilterWhere(['like', '{{%story_comment}}.title', $this->keyword]);
            //$query->orFilterWhere(['like', '{{%story_comment}}.content', $this->keyword]);
            //$query->orFilterWhere(['like', '{{%story}}.game_title', $this->keyword]);
            $query->andFilterWhere(
                    ['or' ,
                        ['like' , '{{%story_comment}}.title' , $this->keyword] ,
                        ['like' , '{{%story_comment}}.content' , $this->keyword],
                        ['like' , '{{%story}}.game_title' , $this->keyword],
                    ]
            );
        }

        if($story_id_arr){
            $query->andFilterWhere(['in' , 'story_id' ,$story_id_arr]);
        }

        return $query;
    }


}
