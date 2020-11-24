<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StoryCommentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '故事评论列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-comment-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create Story Comment', ['create'], ['class' => 'btn btn-success']) ?>
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
                    //'comment_type',
                    'title',
                    //'content:ntext',
                    [
                        'attribute'=>'content',
                        //'label'=>'内容',
                        'format'=>'raw',
                        'value'=>function($model){
                            return "<div style=\"width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis\">".$model->content."</div>";
                            //
                        },
                    ],
                    'from_uid',
                     //'created_at',
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],
                     //'comment_img_id',
                     'heart_val',
                     'likes',
                     'views',
                     'share_num',
                     //'is_plot',
                     //'is_choiceness',
                     //'is_top',
                     //'is_show',

                    [
                        "attribute" => "is_plot",
                        "value" => function ($model) {
                            return backend\models\StoryCommentSearch::dropDown("is_plot_html", $model->is_plot);
                        },
                        "filter" =>  backend\models\StoryCommentSearch::dropDown("is_plot"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],


                    [
                        "attribute" => "is_choiceness",
                        "value" => function ($model) {
                            return backend\models\StoryCommentSearch::dropDown("is_choiceness_html", $model->is_choiceness);
                        },
                        "filter" =>  backend\models\StoryCommentSearch::dropDown("is_choiceness"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],

                    [
                        "attribute" => "is_top",
                        "value" => function ($model) {
                            return backend\models\StoryCommentSearch::dropDown("is_top_html", $model->is_top);
                        },
                        "filter" =>  backend\models\StoryCommentSearch::dropDown("is_top"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],

                    [
                        "attribute" => "is_show",
                        "value" => function ($model) {
                            return backend\models\StoryCommentSearch::dropDown("is_show_html", $model->is_show);
                        },
                        "filter" =>  backend\models\StoryCommentSearch::dropDown("is_show"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],
                    // 'status',
                    // 'update_at',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

</div>
