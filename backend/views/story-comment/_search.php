<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\StoryCommentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-comment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'story_id') ?>

    <?= $form->field($model, 'comment_type') ?>

    <?= $form->field($model, 'content') ?>

    <?= $form->field($model, 'from_uid') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'comment_img_id') ?>

    <?php // echo $form->field($model, 'heart_val') ?>

    <?php // echo $form->field($model, 'is_plot') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'likes') ?>

    <?php // echo $form->field($model, 'is_show') ?>

    <?php // echo $form->field($model, 'is_choiceness') ?>

    <?php // echo $form->field($model, 'is_top') ?>

    <?php // echo $form->field($model, 'views') ?>

    <?php // echo $form->field($model, 'share_num') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
