<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StoryVideo */

$this->title = '创建视频';
$this->params['breadcrumbs'][] = ['label' => 'Story Videos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-video-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
