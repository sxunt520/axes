<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\StoryRecommend */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-recommend-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'story_id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'story_id')->label('所属游戏')->dropDownList(\common\models\Story::getStoryRows(), ['prompt'=>'请选择游戏','style'=>'width:500px']) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'type')->dropDownList(['1'=>'图片','2'=>'视频'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?php
//    $form->field($model, 'cover_url')->widget('common\widgets\file_upload\FileUpload',[
//        'config'=>[
//            //图片上传的一些配置，不写调用默认配置
//            'domain_url' => Yii::getAlias('@static'),////图片域名
//            'serverUrl' => yii\helpers\Url::to(['upload_one','action'=>'uploadimage','is_thumb'=>true,'adv_width'=>720,'adv_height'=>1280]),  //上传服务器地址 is_thumb就否返回生成缩略图
//        ]
//    ])->label('封面图(720*1280px | 9:16 文件格式jpg、png 500k以下,<span style="color: red;">需传入</span>)')
    ?>

    <div class="form-group field-storyrecommend-cover_url required">
        <label class="control-label" for="storyrecommend-cover_url">封面图(图片尺寸>720*1280px,大小<2M)</label>

        <div class="avatar-view" style=" margin-bottom:10px; background: #fff;">
            <img src="<?php echo !empty($model->cover_url)?$model->cover_url:'';?>" alt="上传封面图">
        </div>
        <input type="hidden" value="<?php echo !empty($model->cover_url)?$model->cover_url:'';?>" id="xxx-upimg" name="StoryRecommend[cover_url]">
    </div>

    <?= $form->field($model,'video_url')->textInput()->hiddenInput(['value'=>$model->video_url])->label(false);?>
    <?php
    if($model->video_url){
        $video=strpos($model->video_url, 'http') === false ? (\Yii::getAlias('@static') . $model->video_url) : $model->video_url;
        $video_url='<video width="300" height="auto" controls="controls"><source src="'.$video.'" type="video/mp4"></video>';
    }else{
        $video_url=null;
    }
    echo $form->field($model,'_video_url')->label('推荐视频')->widget(FileInput::classname(), [
                'options' => ['accept' => 'video/*'],
                'pluginOptions' => [
                    // 需要预览的文件格式
                    'previewFileType' => 'video',
                    // 预览的文件
                    'initialPreview' => [$video_url],
                    // 异步上传的接口地址设置
                    'uploadUrl' => \yii\helpers\Url::toRoute(['async-upcos']),
                    //'uploadUrl' => \yii\helpers\Url::toRoute(['async-video']),
                    'uploadAsync' => true,
                    // 最少上传的文件个数限制
                    'minFileCount' => 1,
                    // 最多上传的文件个数限制
                    'maxFileCount' => 1,
                    //'showPreview'=>false,//是否显示整个文件区，自然就无法拖曳文件进行上传了
                ],
                // 一些事件行为
                    'pluginEvents' => [
                        // 上传成功后的回调方法，需要的可查看data后再做具体操作，一般不需要设置
                        "fileuploaded" => "function (event, data, id, index) {
                                    console.log(data);
                                    //console.log(data.response.initialPreview[0]);
                                    //console.log($('input[StoryRecommend][video_url]'));
                                    $(\"input[name='StoryRecommend[video_url]']\").val(data.response.video_url)
                                }",
                    ],
                    //'pluginLoading'=>false,
    ]);?>

    <?= $form->field($model, 'is_show')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?= $form->field($model, 'orderby')->label('排序值:格式数字(数字越大排名越靠前)')->textInput(['maxlength' => 11,'style'=>'width:300px','value'=>!empty($model->orderby)?$model->orderby:100]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= common\widgets\avatar\AvatarWidget::widget(['imageUrl'=>'']); ?>

</div>
