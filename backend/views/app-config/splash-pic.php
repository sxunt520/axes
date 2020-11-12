<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\StoryRecommend */

$this->title = '闪屏图配置';
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="story-recommend-update">

    <?php $form = ActiveForm::begin(); ?>
        <div class="form-group field-storyrecommend-cover_url required">
            <label class="control-label" for="storyrecommend-cover_url">(上传的图片尺寸>720*1280px,大小<1M)</label>

            <div class="avatar-view" style=" margin-bottom:10px; background: #fff;">
                <img src="<?php echo !empty($model->value)?$model->value:'';?>" alt="上传封面图">
            </div>
            <input type="hidden" value="<?php echo !empty($model->value)?$model->value:'';?>" id="xxx-upimg" name="AppConfig[value]">
        </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= common\widgets\avatar\AvatarWidget::widget(['imageUrl'=>'']); ?>

</div>