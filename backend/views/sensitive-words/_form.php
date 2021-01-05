<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SensitiveWords */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sensitive-words-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'word')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
