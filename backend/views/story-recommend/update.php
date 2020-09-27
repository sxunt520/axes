<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\StoryRecommend */

$this->title = '更新故事推荐: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Story Recommends', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="story-recommend-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
