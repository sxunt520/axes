<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\AnnounceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '游戏公告列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-announce-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增游戏公告', ['create'], ['class' => 'btn btn-success']) ?>
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

                    // 'story_id',
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
                                return '--游戏已删除或者不在显示--';
                            }
                            //return \common\models\Story::getStoryRows()[$model->story_id];
                        },
                        "filter" =>  \common\models\Story::getStoryRows(),
                        "headerOptions" => ["width" => "300"]
                    ],

                    //'admin_id',
                    'title',
                    [
                        "attribute" => "user_id",
                        "value" => "user_id",
                        "headerOptions" => ["width" => "140"],
                    ],
                    //'content',
                    // 'views',
                    // 'likes',
                    // 'share_num',
                    // 'pic_cover',

                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],

                    //'is_show:boolean',
                    [
                        "attribute" => "is_show",
                        "value" => function ($model) {
                            return backend\models\AnnounceSearch::dropDown("is_show_html", $model->is_show);
                        },
                        "filter" =>  backend\models\AnnounceSearch::dropDown("is_show"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],

                    [
                        "attribute" => "order_by",
                        "value" => "order_by",
                        "headerOptions" => ["width" => "60"],
                    ],

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

</div>
