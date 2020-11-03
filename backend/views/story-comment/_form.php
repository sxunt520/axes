<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StoryComment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-comment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'story_id')->textInput() ?>
    <?= $form->field($model, 'story_id')->label('所属游戏')->dropDownList(\common\models\Story::getStoryRows(), ['prompt'=>'请选择游戏','style'=>'width:500px']) ?>

    <?php // $form->field($model, 'comment_type')->textInput() ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'from_uid')->textInput(['style'=>'width:220px']) ?>

    <?php // $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'comment_img_id')->textInput(['style'=>'width:220px']) ?>

    <?= $form->field($model, 'heart_val')->textInput(['maxlength' => true,'style'=>'width:220px']) ?>

    <?= $form->field($model, 'likes')->textInput(['maxlength' => true,'style'=>'width:120px']) ?>

    <?= $form->field($model, 'views')->textInput(['maxlength' => true,'style'=>'width:120px']) ?>

    <?= $form->field($model, 'share_num')->textInput(['maxlength' => true,'style'=>'width:120px']) ?>

    <?= $form->field($model, 'is_plot')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?= $form->field($model, 'is_show')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?= $form->field($model, 'is_choiceness')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?= $form->field($model, 'is_top')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?php // $form->field($model, 'update_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
