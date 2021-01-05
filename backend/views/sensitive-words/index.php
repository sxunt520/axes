<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SensitiveWordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '评论敏感词列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensitive-words-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('增加敏感词', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="box box-primary">
        <div class="box-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    'id',
                    'word',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

</div>
