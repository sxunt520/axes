<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\StorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'title') ?>

    <?= $form->field($model, 'intro') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'cover_url') ?>

    <?php // echo $form->field($model, 'video_url') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'admin_id') ?>

    <?php // echo $form->field($model, 'next_updated_at') ?>

    <?php // echo $form->field($model, 'current_chapters') ?>

    <?php // echo $form->field($model, 'total_chapters') ?>

    <?php // echo $form->field($model, 'is_show') ?>

    <?php // echo $form->field($model, 'likes') ?>

    <?php // echo $form->field($model, 'views') ?>

    <?php // echo $form->field($model, 'share_num') ?>

    <?php // echo $form->field($model, 'record_pic') ?>

    <?php // echo $form->field($model, 'game_title') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
