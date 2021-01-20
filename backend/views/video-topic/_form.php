<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;

/* @var $this yii\web\View */
/* @var $model common\models\StoryVideoTopic */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="story-video-topic-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'story_id')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'story_id')->label('所属游戏')->dropDownList(\common\models\Story::getStoryRows(), ['prompt'=>'请选择游戏','style'=>'width:500px']) ?>

    <?= $form->field($model, 'topic_title')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?= $form->field($model,'topic_video_url')->textInput()->hiddenInput(['value'=>$model->topic_video_url])->label(false);?>
    <?php
    if($model->topic_video_url){
        $video=strpos($model->topic_video_url, 'http') === false ? (\Yii::getAlias('@static') . $model->topic_video_url) : $model->topic_video_url;
        $video_url='<video width="300" height="auto" controls="controls"><source src="'.$video.'" type="video/mp4"></video>';
    }else{
        $video_url=null;
    }
    echo $form->field($model,'_topic_video_url')->label('主题视频')->widget(FileInput::classname(), [
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
                                            //console.log($('input[StoryVideoTopic][video_url]'));
                                            $(\"input[name='StoryVideoTopic[topic_video_url]']\").val(data.response.video_url);
                                            
                                            if(data.response.video_cover_flag==true){//如果有生成视频封面gif图
                                                $(\".per_real_img\").html('<img src=\"'+data.response.video_cover_url+'\"/>');
                                                $(\"input[name='StoryVideoTopic[topic_cover]']\").val(data.response.video_cover_url);
                                            }
                                                
                                        }",
        ],
        //'pluginLoading'=>false,
    ]);?>

    <?php // $form->field($model, 'topic_cover')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'topic_cover')->widget('common\widgets\file_upload\FileUpload',[
        'config'=>[
            //图片上传的一些配置，不写调用默认配置
            'domain_url' => Yii::getAlias('@static'),////图片域名
            'serverUrl' => yii\helpers\Url::to(['upload_one','action'=>'uploadimage','is_thumb'=>false,'adv_width'=>720,'adv_height'=>720]),  //上传服务器地址 is_thumb就否返回生成缩略图
        ]
    ])->label('主题封面(上传尺寸420*420px,大小<1M ,可上传gif、jpg、png、webp <span style="color:#FF0000;">建议上传gif动图</span>)') ?>

    <?php // $form->field($model, 'is_show')->textInput() ?>
    <?= $form->field($model, 'is_show')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?php // $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
