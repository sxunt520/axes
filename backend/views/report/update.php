<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Report */

$this->title = '审核举报: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '审核';
?>
<div class="report-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
