<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VideoTopicSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '视频主题列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-video-topic-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增视频主题', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box box-primary">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    //'id',
                    //'story_id',
                    [
                        "attribute" => "id",
                        "value" => "id",
                        "headerOptions" => ["width" => "60"],
                    ],
                    [
                        "attribute" => "story_id",
                        "value" => "story_id",
                        'label' => '游戏id',
                        "headerOptions" => ["width" => "60"],
                    ],
                    [
                        'label' => '所属游戏(游戏名称)',
                        "attribute" => "story_id",
                        "value" => function ($model) {
                            $StoryRows=\common\models\Story::getStoryRows();
                            if(array_key_exists($model->story_id, $StoryRows)){
                                return $StoryRows[$model->story_id];
                            }else{
                                return '--游戏已删除--';
                            }
                            //return \common\models\Story::getStoryRows()[$model->story_id];
                        },
                        "filter" =>  \common\models\Story::getStoryRows(),
                        "headerOptions" => ["width" => "300"]
                    ],
                    //'topic_title',
                    [
                        "attribute" => "topic_title",
                        "value" => "topic_title",
                        "headerOptions" => ["width" => "300"],
                    ],
                    //'topic_cover',
                    [
                        'label' => '主题封面',
                        'format' => [
                            'image',
                            [
                                'width'=>'150',
                                'height'=>'auto'
                            ]
                        ],
                        'value' => function ($model) {
                            $pic=strpos($model->topic_cover, 'http') === false ? (\Yii::getAlias('@static') . $model->topic_cover) : $model->topic_cover;
                            return $pic;
                        }
                    ],
                    'content:ntext',
                     //'is_show',
                    [
                        "attribute" => "is_show",
                        "value" => function ($model) {
                            return backend\models\VideoTopicSearch::dropDown("is_show_html", $model->is_show);
                        },
                        "filter" =>  backend\models\VideoTopicSearch::dropDown("is_show"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],
                     //'created_at',
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

</div>
