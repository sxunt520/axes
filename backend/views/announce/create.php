<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StoryAnnounce */

$this->title = '新增游戏公告';
$this->params['breadcrumbs'][] = ['label' => 'Story Announces', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-announce-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
