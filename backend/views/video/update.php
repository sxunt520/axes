<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideo */

$this->title = 'Update Story Video: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Story Videos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="story-video-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
