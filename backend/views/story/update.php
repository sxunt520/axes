<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Story */

$this->title = '更新故事: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Stories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="story-update">

    <?= $this->render('_form', [
        'model' => $model,
        'model2' => $model2,
        'p1' => $p1,
        'p2' => $p2,
        'id0' => $id0,
        'flag'=>$flag
    ]) ?>

</div>