<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SensitiveKeywordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '敏感词列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensitive-keywords-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增敏感词', ['create'], ['class' => 'btn btn-success']) ?>
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
                    //'desc',
                    [
                        "attribute" => "desc",
                        "value" => "desc",
                        "headerOptions" => ["width" => "200"],
                    ],
                    //'word:ntext',
                    [
                        'attribute'=>'word',
                        //'label'=>'内容',
                        'format'=>'raw',
                        'value'=>function($model){
                            return "<div style=\"width:800px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis\">".$model->word."</div>";
                            //
                        },
                    ],
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

</div>
