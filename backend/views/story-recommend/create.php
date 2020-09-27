<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StoryRecommend */

$this->title = '新增故事推荐';
$this->params['breadcrumbs'][] = ['label' => 'Story Recommends', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-recommend-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
