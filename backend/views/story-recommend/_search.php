<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\StoryRecommendSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-recommend-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'cover_url') ?>

    <?= $form->field($model, 'video_url') ?>

    <?php // echo $form->field($model, 'story_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'is_show') ?>

    <?php // echo $form->field($model, 'orderby') ?>

    <?php // echo $form->field($model, 'likes') ?>

    <?php // echo $form->field($model, 'views') ?>

    <?php // echo $form->field($model, 'share_num') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
