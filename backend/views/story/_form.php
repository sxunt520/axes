<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\Story */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="<?php echo $flag==0?'active':'';?>"><a href="#tab_1" data-toggle="tab" aria-expanded="<?php echo $flag==0?'true':'false';?>">故事内容</a></li>
        <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">游戏内容</a></li>
        <?php if($id0!=''&&$model2!=''){?><li class="<?php echo $flag==1?'active':'';?>"><a href="#tab_3" data-toggle="tab" aria-expanded="<?php echo $flag==1?'true':'false';?>">故事多图</a></li><?php }?>
    </ul>
    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'tab-content','enctype'=>'multipart/form-data']
    ]); ?>
    <div class="tab-pane <?php echo $flag==0?'active':'';?>" id="tab_1">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'intro')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'type')->dropDownList(['1'=>'图片','2'=>'视频'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

        <?php // $form->field($model, 'cover_url')->widget('yidashi\webuploader\Cropper',['options'=>['boxId' => 'picker', 'previewWidth'=>720, 'previewHeight'=>'auto']]) ?>
        <?php //$form->field($model, 'record_pic')->widget('yidashi\webuploader\Cropper2',['options'=>['boxId' => 'picker2', 'previewWidth'=>800, 'previewHeight'=>'auto']]) ?>

        <?php
//        $adv_width=720;
//        $adv_height=1280;
//        $valueArr=array();
//        $valueArr['value']=$model->cover_url;
//        $valueArr['config']['inputName']='Story[cover_url]';
//        if($adv_width&&$adv_height){
//            $valueArr['config']['adv_width']=$adv_width;
//            $valueArr['config']['adv_height']=$adv_height;
//             $valueArr2['config']['is_thumb']=true;
//        }
//        echo common\widgets\file_upload\FileUpload2::widget($valueArr);
//
//        $adv_width2=500;
//        $adv_height2=300;
//         $valueArr2=array();
//         $valueArr2['value']=$model->record_pic;
//         $valueArr2['config']['inputName']='Banner[record_pic]';
//         if($adv_width&&$adv_height){
//             $valueArr2['config']['adv_width']=$adv_width2;
//             $valueArr2['config']['adv_height']=$adv_height2;
//             $valueArr2['config']['is_thumb']=true;
//         }
//         echo common\widgets\file_upload\FileUpload2::widget($valueArr2);

        ?>

        <?= $form->field($model, 'cover_url')->widget('common\widgets\file_upload\FileUpload',[
            'config'=>[
                //图片上传的一些配置，不写调用默认配置
                'domain_url' => Yii::getAlias('@static'),////图片域名
                'serverUrl' => yii\helpers\Url::to(['upload_one','action'=>'uploadimage','is_thumb'=>true,'adv_width'=>720,'adv_height'=>1280]),  //上传服务器地址 is_thumb就否返回生成缩略图
            ]
        ])->label('故事封面图(720*1280px|9:16 文件格式jpg、png 3MB以下') ?>

        <?= $form->field($model, 'record_pic')->widget('common\widgets\file_upload\FileUpload',[
            'config'=>[
                //图片上传的一些配置，不写调用默认配置
                'domain_url' => Yii::getAlias('@static'),////图片域名
                'serverUrl' => yii\helpers\Url::to(['upload_one','action'=>'uploadimage','is_thumb'=>true,'adv_width'=>720,'adv_height'=>430]),  //上传服务器地址 is_thumb就否返回生成缩略图
            ]
        ])->label('旅行记录图(720*430px|5:3 文件格式jpg、png 3MB以下') ?>


        <?= $form->field($model, 'is_show')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    </div>
    <div class="tab-pane" id="tab_2">
        <?php // $form->field($model, 'other')->widget('kucha\ueditor\UEditor', ['options' => ['style' => 'height:200px']]) ?>
        <?= $form->field($model, 'game_title')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'current_chapters')->textInput() ?>
        <?= $form->field($model, 'total_chapters')->textInput() ?>
        <?= $form->field($model, 'next_updated_at')->widget(
            \trntv\yii\datetime\DateTimeWidget::className(),
            [
                'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
                'phpMomentMapping' => ['yyyy-MM-dd HH:mm:ss' => 'YYYY-MM-DD HH:mm:ss'],
                'locale' => 'zh-cn'
            ]
        ) ?>
    </div>
    <?php if($id0!=''&&$model2!=''){?>
        <div class="tab-pane <?php echo $flag==1?'active':'';?>" id="tab_3">
            <?= $form->field($model2, 'edify_url')->label('渲染画册')->widget(FileInput::classname(), [
                'options' => ['multiple' => true],
                //'template' => '<img src="{image}" class="file-preview-image" style="width:auto;height:160px;">',
                'pluginOptions' => [
                    // 需要预览的文件格式
                    'previewFileType' => 'image',
                    // 预览的文件
                    //'initialPreview' => ['图片1', '图片2', '图片3'],
                    'initialPreview' => $p1,
                    // 需要展示的图片设置，比如图片的宽度等
                    //'initialPreviewConfig' => ['width' => '120px'],
                    'initialPreviewConfig' => $p2,
                    // 是否展示预览图
                    'initialPreviewAsData' => true,
                    // 异步上传的接口地址设置
                    'uploadUrl' => \yii\helpers\Url::toRoute(['async-image']),
                    // 异步上传需要携带的其他参数，比如商品id等
                    'uploadExtraData' => [
                        'edify_id' => $id0,
                    ],
                    'uploadAsync' => true,
                    // 最少上传的文件个数限制
                    'minFileCount' => 1,
                    // 最多上传的文件个数限制
                    'maxFileCount' => 10,
                    // 是否显示移除按钮，指input上面的移除按钮，非具体图片上的移除按钮
                    'showRemove' => true,
                    // 是否显示上传按钮，指input上面的上传按钮，非具体图片上的上传按钮
                    'showUpload' => true,
                    //是否显示[选择]按钮,指input上面的[选择]按钮,非具体图片上的上传按钮
                    'showBrowse' => true,
                    // 展示图片区域是否可点击选择多文件
                    'browseOnZoneClick' => true,
                    // 如果要设置具体图片上的移除、上传和展示按钮，需要设置该选项
                    'fileActionSettings' => [
                        // 设置具体图片的查看属性为false,默认为true
                        'showZoom' => false,
                        // 设置具体图片的上传属性为true,默认为true
                        'showUpload' => true,
                        // 设置具体图片的移除属性为true,默认为true
                        'showRemove' => true,
                    ],
                ],
                // 一些事件行为
                'pluginEvents' => [
                    // 上传成功后的回调方法，需要的可查看data后再做具体操作，一般不需要设置
                    "fileuploaded" => "function (event, data, id, index) {
            console.log(data);
            }",
                ],
            ]);
            ?>
        </div>
    <?php };?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>



<?php /*
<div class="story-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'intro')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'type')->textInput() ?>
    <?= $form->field($model, 'cover_url')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'record_pic')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'video_url')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'is_show')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>
    <?= $form->field($model, 'admin_id')->textInput() ?>




    <?= $form->field($model, 'game_title')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'current_chapters')->textInput() ?>
    <?= $form->field($model, 'total_chapters')->textInput() ?>
    <?= $form->field($model, 'next_updated_at')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
 */ ?>