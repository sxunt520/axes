<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideoTopic */

$this->title = '更新视频主题: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '视频主题列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="story-video-topic-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
