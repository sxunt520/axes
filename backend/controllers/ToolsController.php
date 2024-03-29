<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;

use backend\models\Upload;
use yii\web\UploadedFile;
use backend\components\Ffmpeg;

class ToolsController extends Controller{
    
/**
* 文件上传
* 我们这里上传成功后把图片的地址进行返回
*/
public function actionUpload ()
{   
    $model = new Upload();
    $uploadSuccessPath = "";
    if (Yii::$app->request->isPost) {
    $model->file = UploadedFile::getInstance($model, "file");
    //文件上传存放的目录
    $dir = "../../static/uploads2/".date("Ymd");
        if (!is_dir($dir))
            mkdir($dir);
            if ($model->validate()) {
                //文件名
                $fileName = date("HiiHsHis").$model->file->baseName . "." . $model->file->extension;
                $dir = $dir."/". $fileName;
                $model->file->saveAs($dir);
                $uploadSuccessPath = "/uploads2/".date("Ymd")."/".$fileName;
            }
        }
        return $this->render("upload", [
            "model" => $model,
            "uploadSuccessPath" => $uploadSuccessPath,
        ]);
    }

/**
 * 批量上传Demo
 * http://backend.xiaoego.local/tools/upload2
 */
public function actionUpload2 ()
{
    // 假设商品的图片是 $relationBanners,$id是商品的id
    // $relationBanners的数据结构如：
    /**
     * Array
     *(
     * [0] => Array
     * (
     * [id] => 1484314
     * [goods_id] => 1173376
     * [banner_url] => ./uploads/20160617/146612713857635322241f2.png
     * )
     *
     *)
     */
    $id=3;
    $relationBanners = \common\models\Banner::find()->where(['goods_id' => $id])->asArray()->all();
    // 对商品banner图进行处理
    $p1 = $p2 = [];
    if ($relationBanners) {
        foreach ($relationBanners as $k => $v) {
            //$p1[$k] = $v['banner_url'];
            $p1[$k] = '<img src="http://img.sxunt.com'.$v['banner_url'].'" class="file-preview-image" style="width:auto;height:160px;">';
            $p2[$k] = [
                'url' => \yii\helpers\Url::toRoute('delete'),
                'key' => $v['id'],
            ];
        }
    }
    $model = new \common\models\Banner;
    return $this->render('upload2', [
        'model' => $model,
        'p1' => $p1,
        'p2' => $p2,
        'id' => $id
    ]);
    
}

public function actionAsyncImage ()
{
    // 商品ID
    $id = Yii::$app->request->post('goods_id');
    $p1 = $p2 = [];
    if (empty($_FILES['Banner']['name']) || empty($_FILES['Banner']['name']['banner_url']) || !$id) {
        echo '{}';
        return;
    }
    for ($i = 0; $i < count($_FILES['Banner']['name']['banner_url']); $i++) {
        $url = 'delete';

        $model = new \common\models\Banner();
        $uploadSuccessPath = "";
        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstance($model, "banner_url");
            //文件上传存放的目录
            $dir = "../../static/uploads2/".date("Ymd").'/';
            
            if(!file_exists($dir)){
                mkdir($dir,0777);
            }
            //文件名
            $fileName = date("Ymdhis").'_'.uniqid(). "." . $file->extension;
            $dir = $dir."/". $fileName;
            $file->saveAs($dir);
            $uploadSuccessPath = "/uploads2/".date("Ymd")."/".$fileName;
           
        }
        
        $imageUrl = $uploadSuccessPath; //调用图片接口上传后返回图片地址
        // 图片入库操作，此处不可以批量直接入库，因为后面我们还要把key返回 便于图片的删除
        //$model = new \common\models\Banner;
        $model->goods_id = $id;
        $model->banner_url = $imageUrl;
        $key = 0;
        if ($model->save(false)) {
            $key = $model->id;
        }
        // $pathinfo = pathinfo($imageUrl);
        // $caption = $pathinfo['basename'];
        // $size = $_FILES['Banner']['size']['banner_url'][$i];
        //$p1[$i] = $imageUrl;
        $p1[$i] ='<img src="http://img.sxunt.com'.$imageUrl.'" class="file-preview-image" style="width:auto;height:160px;">';
        $p2[$i] = ['url' => $url, 'key' => $key,'width' => '120px'];
    }
    echo json_encode([
        'initialPreview' => $p1,
        'initialPreviewConfig' => $p2,
        'append' => true,
    ]);
    return;
}

    public function actionDelete ()
    {
        if ($id = Yii::$app->request->post('key')) {
            
            $model = \common\models\Banner::find()->where(['id' => $id])->one();
            
            if($model->delete()){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true];
            }else{
                return ['success' => false];
            }
        }else{
            return ['success' => false];
        }    
    }

    /**
     *Time:2020/11/23 15:40
     *Author:始渲
     *Remark:ffmpe生成视频第一秒为封面截图
     * @params:
     */
    public function actionVideoCover()
    {
        $savePath=Yii::getAlias('@staticroot').'/uploads/';
        $file_dir='video_cover_'.date("Ymd");
        $fileName='videoimg_'.uniqid().'.jpg';

        $path = $savePath.$file_dir;

        //创建目录
        if(!is_dir($path)){
            mkdir($path,0777);
        }

        //ffmpeg配置
        if (YII_ENV=='dev') {//本地win
            $ffmpeg_config_arr=[
                //绑定插件
                'ffmpeg.binaries'  => 'D:\down\ffmpeg-N-99973-g0066bf4d1a-win64-gpl-shared-vulkan\bin\ffmpeg.exe',
                'ffprobe.binaries' => 'D:\down\ffmpeg-N-99973-g0066bf4d1a-win64-gpl-shared-vulkan\bin\ffprobe.exe'
            ];
            $host='http://api.axes.com';
            //$video_url='http://axe-video-1257242485.cos.ap-guangzhou.myqcloud.com/axe_uploads/video_20201119/5fb64e250e1ad.mp4';
            $video_url='D:\NEXT\test\xxxx.mp4';
        } else {//线上linux
            $ffmpeg_config_arr=[
                //绑定插件
                'ffmpeg.binaries'  => '/usr/local/ffmpeg/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/ffmpeg/bin/ffprobe',
                //'timeout'          => 3600, // The timeout for the underlying process
                //'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
            ];
            $host='http://81.71.11.205:8000';
            $video_url='/data/site/axes/api/web/uploads/video_20200924/20200924043502_5f6c5a3630ed7.mp4';
        }

        $ffmpeg = \FFMpeg\FFMpeg::create(
            $ffmpeg_config_arr
        );
        $video = $ffmpeg->open($video_url);
        $video
            ->filters()
            ->resize(new \FFMpeg\Coordinate\Dimension(720, 1280))
            ->synchronize();
        //生成视频截图
        $r=$video
            ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(1))
            ->save($path.'/'.$fileName);

        if($r){
            echo $path.'/'.$fileName;
            echo '<br />';
            echo $host.'/uploads/'.$file_dir.'/'.$fileName;
        }else{
            echo '生成失败';
        }
    }

    /**
     *Time:2020/11/23 17:31
     *Author:始渲
     *Remark:设置视频封面demo
     */
    public function actionSetVideoCover()
    {
        $fromSeconds = (int)Yii::$app->request->get('fromSeconds');
        if($fromSeconds>0){
            $_fromSeconds=$fromSeconds;
        }else{
            $_fromSeconds=1;
        }

        $video_cover_url=Ffmpeg::getVideoCover('https://axe-video-1257242485.cos.ap-guangzhou.myqcloud.com/axe_uploads/video_20201009/20201009064135_5f803e5f91c65.mp4',$_fromSeconds);
        if($video_cover_url){
            echo '<img width="500" src="'.$video_cover_url.'" />';
            //echo $video_cover_url;
        }else{
            echo '获取视频封面失败';
        }
    }

}