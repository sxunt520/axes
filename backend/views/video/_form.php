<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-video-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'story_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'video_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'video_cover')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'video_type')->textInput() ?>

    <?= $form->field($model, 'is_show')->textInput() ?>

    <?= $form->field($model, 'views')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'likes')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'share_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
