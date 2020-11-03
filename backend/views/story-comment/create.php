<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StoryComment */

$this->title = 'Create Story Comment';
$this->params['breadcrumbs'][] = ['label' => 'Story Comments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="story-comment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
