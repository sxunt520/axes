<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SensitiveKeywords */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '敏感词列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sensitive-keywords-view">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'word:ntext',
            'desc',
        ],
    ]) ?>

</div>
