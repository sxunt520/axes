<?php
/**
 * @see Yii中文网  http://www.yii-china.com
 * @author Xianan Huang <Xianan_huang@163.com>
 * 图片上传组件
 * 如何配置请到官网（Yii中文网）查看相关文章
 */
 
use yii\helpers\Html;
?>
<div class="per_upload_con" data-url="<?=$config['serverUrl']?>" style="width:auto;height:auto;">
    <div class="per_real_img <?=$attribute?>" domain-url = "<?=$config['domain_url']?>" style="width:auto;height:auto;"><?=isset($inputValue)&&$inputValue!=''?'<img src="'.$config['domain_url'].$inputValue.'">':''?></div>
    <div class="per_upload_text">
        <p class="upbtn" ><a id="<?=$attribute?>" href="javascript:;" class="choose_btn">选择图片</a></p>
    </div>
    <input up-id="<?=$attribute?>" type="hidden" name="<?=$inputName?>" upname='<?=$config['fileName']?>' value="<?=isset($inputValue)?$inputValue:''?>" filetype="img" />
</div>
<div style="clear:both;"></div>