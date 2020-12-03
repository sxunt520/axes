<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Report */

$this->title = 'Create Report';
$this->params['breadcrumbs'][] = ['label' => 'Reports', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
