/**
 * Created by zhang on 2017/7/13.
 */

//删除图片
function delpic(obj){
    var pic = $(obj).parent().find('input').val();
    var picture_id = $(obj).attr('picture_id');
    var trueDeleteStr = trueDelete == 'true' ? '确认后，服务器上的真实图片也会被删除' : '';
    if(confirm('确认要删除图片吗？')){
        if(trueDelete == 'true'){
            console.log(serverUrl + '?action=delete&pic='+pic+'&picture_id='+picture_id);
            pic = encodeURIComponent(pic);// ↓ index.php?r=product%2Fupload_more?action=delete&pic=http%3A%2F%2Fshop.local%2Fuploads%2F2017%2F10%2F26%2F20171026151919138807.jpg
            $.get(serverUrl + '?action=delete&pic='+pic+'&picture_id='+picture_id, function(res){
                if(res == 'ok'){
                    $(obj).parent().remove();
                }
            });
        }else{
            $(obj).parent().remove();
        }
    }
}

new AjaxUpload($("#pics_button"),{
    action: serverUrl,
    type:"POST",
    data:{img_w: img_width, img_h: img_height},
    autoSubmit:true,
    responseType:'json',//"json",
    name : fileData,
    onChange: function(file, ext){
        var o = this._input;
        var oid = $(o).attr('id');
        //检查格式
        if (!(ext && /^(jpg|jpeg|JPG|JPEG|PNG|gif)$/i.test(ext))) {
            alert('文件格式不正确');
            return false;
        }else{
            //判断图片是否合法
            var oFile = document.getElementById(oid).files[0];
            if(parseInt(oFile.size) > allow_size ){
                var size = allow_size / 1024 / 1024;
                alert('大小不能超过'+( Math.round(size*10)/10)+'M');
                return false;
            }
        }
        return true;
    },
    onComplete: function(file, resp){ //完成后回调函数
        console.log(resp);
        if(typeof(upload_callback) == 'function'){
            upload_callback(resp);
        }else{
            var html = '<div class="col-xs-6" style="position: relative;width:200px;height:200px; float:left;"><span class="del_pic" onclick="delpic(this)"  picture_id="-1"></span>';
            html += '<input type="hidden" value="'+resp.url+'" name="'+inputName+'[]" ><input type="hidden" value="'+resp.thumb_url+'" name="picture_thumb_url[]" ><a class="thumbnail" title="点击查看大图" href="'+img_host+resp.url+'" target="_blank"><img class="img-responsive" src="'+img_host+resp.thumb_url+'"></a>';
            html += '</div>'
            $('#pics_preview').append(html );
        }
    }
});