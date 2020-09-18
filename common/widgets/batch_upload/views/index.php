<script>
    var allow = 1;
    var allow_size ='<?php echo $config['maxSize']?>';
    var serverUrl = '<?php echo $config['serverUrl']?>';
    var fileData = '<?php echo $config['fieldName']?>';
    var inputName = '<?=$inputName?>';
    var trueDelete = '<?=$config['trueDelete']?>';
    var img_host= '<?=$config['img_host']?>';
    var img_width=<?=$options['img_width']?>;
    var img_height=<?=$options['img_height']?>;
</script>

<div id="main" style="width:100%;">
    <div class="demo" style="width:100%;padding-bottom:0px; margin-bottom:0;">
        <div id="pics_button" class="btn">
            <span>添加图片</span>
        </div>
        <p>最大<?php //echo ceil($config['maxSize']/1024/1024).'M'?>800k，支持jpg，gif，png格式。</p>

        <div class="row" id="pics_preview">
            <?php if(!empty($value))://var_dump($value);exit;?>
                <?php //$value = json_decode($value, 1);?>
                <?php foreach($value as $k => $r):?>
                    <div class="col-xs-6" style="position: relative;width:200px;height:200px; float:left;">
                        <span class="del_pic"  onclick="delpic(this)" picture_id="<?=$r['picture_id']?>"></span>
                        <!--
                        <input type="hidden" value="<?=$r['picture_url']?>" name="<?=$inputName.'_'?>[]" >
                        <input type="hidden" value="<?=$r['picture_thumb_url']?>" name="picture_thumb_url_[]" >
                        -->
                        <a title="点击查看大图" href="<?=$config['img_host']?><?=$r['picture_url']?>" class="thumbnail" target="_blank"><img src="<?=$config['img_host']?><?=$r['picture_thumb_url']?>"></a>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
            <!--<a href="javascript:void(0);" style="display:inline-block;width:100px;height:100px;background:#ddd;font-size:120px;line-height:100px;text-align:center;color:#aaa;" id="pics_button">+</a>-->
        </div>

    </div>
</div>
