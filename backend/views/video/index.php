<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\VideoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '视频管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-video-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create Story Video', ['create'], ['class' => 'btn btn-success']) ?>
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
                        'label' => '游戏id',
                        "attribute" => "story_id",
                        "value" => "story_id",
                        "headerOptions" => ["width" => "60"],
                    ],
                    'title',
                    //'video_url:url',
                    [
                        'label' => '游戏视频',
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

                    //'video_cover',
                    [
                        'label' => '视频封面图',
                        'format' => [
                            'image',
                            [
                                'width'=>'150',
                                'height'=>'auto'
                            ]
                        ],
                        'value' => function ($model) {
                            $pic=strpos($model->video_cover, 'http:') === false ? (\Yii::getAlias('@static') . $model->video_cover) : $model->video_cover;
                            return $pic;
                        }
                    ],
                    // 'video_type',
                    // 'is_show',
                    // 'views',
                    // 'likes',
                    // 'share_num',
                    // 'created_at',

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
