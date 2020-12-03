<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '举报审核列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create Report', ['create'], ['class' => 'btn btn-success']) ?>
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
                        "headerOptions" => ["width" => "100"],
                    ],
                    [
                        "attribute" => "report_from_uid",
                        "value" => "report_from_uid",
                        "headerOptions" => ["width" => "100"],
                    ],
                    [
                        "attribute" => "report_to_uid",
                        "value" => "report_to_uid",
                        "headerOptions" => ["width" => "100"],
                    ],
                    //'type',
                    //'event_id',
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
                     //'created_at',
                    [
                        'attribute' => 'created_at',
                        'value' => function ($model) {
                            return date('Y-m-d H:i:s', $model->created_at);
                        },
                        'filter'=>false,
                    ],
                     //'status',
                    [
                        "attribute" => "status",
                        "value" => function ($model) {
                            return backend\models\ReportSearch::dropDown("status", $model->status);
                        },
                        "filter" =>  backend\models\ReportSearch::dropDown("status"),
                        "headerOptions" => ["width" => "80"],
                        "format" => "raw",
                    ],

                    //['class' => 'yii\grid\ActionColumn'],
                    [
                        "class" => "yii\grid\ActionColumn",
                        //"template" => "{get-xxx} {view} {update}",
                        "template" => "{update}",
                        "header" => "审核操作",
                        "buttons" => [
                            "update" => function ($url, $model, $key) {
                                return Html::a("审核", $url, ["title" => "审核"] );
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
