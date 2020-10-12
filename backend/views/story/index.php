<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '游戏列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-index">

    <?php  //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增游戏', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box box-primary">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    //'id',
                    [
                        "attribute" => "id",
                        "value" => "id",
                        "headerOptions" => ["width" => "60"],
                    ],
                    // [
                    //     "attribute" => "type",
                    //     "value" => function ($model) {
                    //         return backend\models\StorySearch::dropDown("type", $model->type);
                    //     },
                    //     "filter" =>  backend\models\StorySearch::dropDown("type"),
                    //     "headerOptions" => ["width" => "80"]
                    // ],

                    //'intro:ntext',

//                    [
//                        'attribute' => 'type',
//                        'value' => function ($model) {
//                            if($model->type==1){
//                                return '图片';
//                            }else if($model->type==2){
//                                return '视频';
//                            }else{
//                                return '其它';
//                            }
//                        },
//                    ],

                    [
                        'label' => '旅行记录图',
                        'format' => [
                            'image',
                            [
                                'width'=>'150',
                                'height'=>'auto'
                            ]
                        ],
                        'value' => function ($model) {
                            $pic=strpos($model->record_pic, 'http') === false ? (\Yii::getAlias('@static') . $model->record_pic) : $model->record_pic;
                            return $pic;
                        }
                    ],

                    //'cover_url:url',
                    // [
                    //     'label' => '故事封面图',
                    //     'format' => [
                    //         'image',
                    //         [
                    //             'width'=>'150',
                    //             'height'=>'auto'
                    //         ]
                    //     ],
                    //     'value' => function ($model) {
                    //         $pic=strpos($model->cover_url, 'http:') === false ? (\Yii::getAlias('@static') . $model->cover_url) : $model->cover_url;
                    //         return $pic;
                    //     }
                    // ],

                    // 'video_url:url',
                    // [
                    //     'label' => '故事视频',
                    //     'attribute'=>'video_url',
                    //     'value' => function ($model) {
                    //         $pic=strpos($model->video_url, 'http:') === false ? (\Yii::getAlias('@static') . $model->video_url) : $model->video_url;
                    //         $video_xxx= '<video width="150" height="auto" controls="controls"><source src="'.$pic.'" type="video/mp4"></video>';
                    //         return $video_xxx;
                    //     },
                    //     "format" => "raw",
                    //     'filter'=>false,
                    //     'enableSorting'=>false,
                    // ],

                    // [
                    //     'label' => '故事标题',
                    //     "attribute" => "title",
                    //     "value" => "title",
                    //     "headerOptions" => ["width" => "200"],
                    // ],
                     //'updated_at',
                    // 'admin_id',
                    [
                        "attribute" => "game_title",
                        "value" => "game_title",
                        "headerOptions" => ["width" => "200"],
                    ],
                    [
                        "attribute" => "current_chapters",
                        "value" => "current_chapters",
                        "headerOptions" => ["width" => "80"],
                    ],
                    [
                        "attribute" => "total_chapters",
                        "value" => "total_chapters",
                        "headerOptions" => ["width" => "80"],
                    ],
                    //'is_show:boolean',
                    [
                        "attribute" => "is_show",
                        "value" => function ($model) {
                            return backend\models\StorySearch::dropDown("is_show_html", $model->is_show);
                        },
                        "filter" =>  backend\models\StorySearch::dropDown("is_show"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],

                    // 'next_updated_at:datetime',
                    [
                        'attribute' => 'next_updated_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],

                    //'created_at:datetime',
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],

                    [
                        "attribute" => "likes",
                        "value" => "likes",
                        "headerOptions" => ["width" => "80"],
                        'filter'=>false,
                    ],
                    [
                        "attribute" => "views",
                        "value" => "views",
                        "headerOptions" => ["width" => "80"],
                        'filter'=>false,
                    ],
                    [
                        "attribute" => "share_num",
                        "value" => "share_num",
                        "headerOptions" => ["width" => "80"],
                        'filter'=>false,
                    ],
                     //'record_pic',
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
