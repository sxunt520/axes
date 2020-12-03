<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Report */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-form">

    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-striped table-bordered detail-view">
        <tbody>
        <tr><th>举报人id</th><td><?= !empty($model->report_from_uid)?$model->report_from_uid:'';?></td></tr>
        <tr><th>被举报人id</th><td><?= !empty($model->report_to_uid)?$model->report_to_uid:'';?></td></tr>
        <tr><th>举报标题</th><td><?= !empty($model->title)?$model->title:'';?></td></tr>
        <tr><th>举报内容</th><td><?= !empty($model->content)?$model->content:'';?></td></tr>
        <tr><th>举报时间</th><td><?= !empty($model->created_at)? date("Y-m-d H:i:s",$model->created_at):'';?></td></tr>
        <tr><th>审核状态</th>
            <td>
                <?php
                if($model->status==1){
                    echo '已审核';
                }else{
                   echo '未审核';
                }
                ?>
            </td></tr>
        </tbody>
    </table>

    <?php // $form->field($model, 'report_from_uid')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'report_to_uid')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'type')->textInput() ?>

    <?php // $form->field($model, 'event_id')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?php // $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->label('审核操作')->dropDownList(['0'=>'未审核','1'=>'已审核'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : '操作', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
