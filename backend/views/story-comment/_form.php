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

    <?= $form->field($model, 'is_top')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>

    <?= $form->field($model, 'is_choiceness')->label('是否是精选<span style="color:#DD4B39;"> (精选了以后，将会在首页推荐中根据故事游戏推荐出现!)</span>')->dropDownList([0=>'否',1=>'是'], ['prompt'=>'未选择','style'=>'width:120px']) ?>
    <div id="choice_box">
        <?= $form->field($model, 'choice_content')->label('精选内容文案<span style="color:#DD4B39;"> (可以根据上面用户评论的内容，复制编辑添加!)</span>')->textInput(['maxlength' => 70]) ?>

        <div class="form-group field-storycomment-choice_img_url">
            <label class="control-label" for="storycomment-choice_img_url">精选推荐图<span style="color:#DD4B39;"> (上传的图片尺寸>420*420px,大小<1M)</span></label>

            <div class="avatar-view" style=" margin-bottom:10px; background: #fff;">
                <img id="video_cover_img" src="<?php echo !empty($model->choice_img_url)?$model->choice_img_url:'';?>" alt="上传精选图">
            </div>
            <input type="hidden" value="<?php echo !empty($model->choice_img_url)?$model->choice_img_url:'';?>" id="xxx-upimg" name="StoryComment[choice_img_url]">
        </div>
    </div>

    <?php // $form->field($model, 'update_at')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?= common\widgets\avatar_square\AvatarWidget::widget(['imageUrl'=>'']); ?>

</div>


<script type="text/javascript">

    $(document).ready(function(){

        var lode_is_choiceness=$("#storycomment-is_choiceness").val();
        if(lode_is_choiceness==0){
            $("#choice_box").hide();
        }

        $("#storycomment-is_choiceness").on("change", function(){
            //alert($(this).val());
            var is_choiceness=$(this).val();
            console.log(is_choiceness);
            if(is_choiceness==1){
                $("#choice_box").show("slow");
            }else{
                $("#choice_box").hide("slow");
            }
            //window.location.href = "/admin/adv/<?php echo Yii::$app->controller->action->id;?>?type_id="+ $(this).val();
        });

    });
</script>