<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StoryComment */

$this->title = '更新用户评论: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Story Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="story-comment-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
