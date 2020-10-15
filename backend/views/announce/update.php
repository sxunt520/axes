<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StoryAnnounce */

$this->title = '更新游戏公告: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Story Announces', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="story-announce-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
