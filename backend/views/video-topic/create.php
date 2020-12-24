<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StoryVideoTopic */

$this->title = '新增视频主题';
$this->params['breadcrumbs'][] = ['label' => '视频主题列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-video-topic-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
