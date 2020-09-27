<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StoryRecommendSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '故事列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-recommend-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增故事', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box box-primary">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
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
                            return \common\models\Story::getStoryRows()[$model->story_id];
                        },
                        "filter" =>  \common\models\Story::getStoryRows(),
                        "headerOptions" => ["width" => "300"]
                    ],
                    [
                        "attribute" => "type",
                        "value" => function ($model) {
                            return backend\models\StoryRecommendSearch::dropDown("type", $model->type);
                        },
                        "filter" =>  backend\models\StoryRecommendSearch::dropDown("type"),
                        "headerOptions" => ["width" => "80"]
                    ],
                    [
                        "attribute" => "title",
                        "value" => "title",
                        "headerOptions" => ["width" => "300"],
                    ],
                    [
                        'label' => '推荐封面',
                        'format' => [
                            'image',
                            [
                                'width'=>'150',
                                'height'=>'auto'
                            ]
                        ],
                        'value' => function ($model) {
                            $pic=strpos($model->cover_url, 'http:') === false ? (\Yii::getAlias('@static') . $model->cover_url) : $model->cover_url;
                            return $pic;
                        }
                    ],
                    [
                        'label' => '推荐视频',
                        'attribute'=>'video_url',
                        'value' => function ($model) {
                            $pic=strpos($model->video_url, 'http:') === false ? (\Yii::getAlias('@static') . $model->video_url) : $model->video_url;
                            $video_xxx= '<video width="150" height="auto" controls="controls"><source src="'.$pic.'" type="video/mp4"></video>';
                            return $video_xxx;
                        },
                        "format" => "raw",
                        'filter'=>false,
                        'enableSorting'=>false,
                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],
                    [
                        "attribute" => "is_show",
                        "value" => function ($model) {
                            return backend\models\StoryRecommendSearch::dropDown("is_show_html", $model->is_show);
                        },
                        "filter" =>  backend\models\StoryRecommendSearch::dropDown("is_show"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],
                    [
                        "attribute" => "orderby",
                        "value" => "orderby",
                        "headerOptions" => ["width" => "60"],
                    ],
                    // 'likes',
                    // 'views',
                    // 'share_num',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        "headerOptions" => ["width" => "80"],
                        //'template' => '{view}{update}{delete}',
                        'template' => '{update}{delete}'
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
