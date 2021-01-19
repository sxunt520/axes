<?php
namespace backend\components;

use Yii;

class Ffmpeg{

    public function __construct()
    {

    }

    /**
     *Time:2020/11/23 17:19
     *Author:始渲
     *Remark:ffmpe生成视频第一秒为封面截图到本地，然后上传到cos,最后返回cos地址
     * @params:$video_url 视频地址  $fromSeconds：在视频多少秒成成截图
     * @return:$video_cover
     */
    public static function getVideoCover($video_url,$fromSeconds=1)
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
            //$host='http://api.axes.com';
            //$video_url='http://axe-video-1257242485.cos.ap-guangzhou.myqcloud.com/axe_uploads/video_20201119/5fb64e250e1ad.mp4';
            //$video_url='D:\NEXT\test\xxxx.mp4';
        } else {//线上linux
            $ffmpeg_config_arr=[
                //绑定插件
                'ffmpeg.binaries'  => '/usr/local/ffmpeg/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/ffmpeg/bin/ffprobe',
                //'timeout'          => 3600, // The timeout for the underlying process
                //'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
            ];
            //$host='http://81.71.11.205:8000';
            //$video_url='/data/site/axes/api/web/uploads/video_20200924/20200924043502_5f6c5a3630ed7.mp4';
        }
        $host=Yii::getAlias('@api_host');

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
            ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($fromSeconds))
            ->save($path.'/'.$fileName);

        if($r){//返回生成的视频封面图
//            echo $path.'/'.$fileName;
//            echo '<br />';
//            echo $host.'/uploads/'.$file_dir.'/'.$fileName;
            $video_cover=$path.'/'.$fileName;//生成的本地图片绝对地址
            $video_host_cover=$host.'/uploads/'.$file_dir.'/'.$fileName;//生成的本地图片host地址

            //上传到cos
            $video_cover_cos_url=SELF::uploadtocos($video_cover);
            if($video_cover_cos_url){
                return $video_cover_cos_url;
            }else{//上传到cos失败返回本地服务器的图片地址
               return $video_host_cover;
            }
        }else{
            return false;
        }

    }

    //上传本地封面图到cos，返回cos地址
    public static function uploadtocos($video_local_cover){

        $secretId = \Yii::$app->params['tencent_cos']['secretId']; //"云 API 密钥 SecretId";
        $secretKey = \Yii::$app->params['tencent_cos']['secretKey']; //"云 API 密钥 SecretKey";
        $region = "ap-guangzhou"; //设置一个默认的存储桶地域
        $cosClient = new \Qcloud\Cos\Client(
            array(
                'region' => $region,
                'schema' => 'http', //协议头部，默认为http
                'credentials'=> array(
                    'secretId'  => $secretId ,
                    'secretKey' => $secretKey)));
        //$local_path = "/Applications/XAMPP/web/demo/jjjj.png";
        $local_path = $video_local_cover;
        try {
            $result = $cosClient->upload(
                $bucket = 'axe-video-1257242485', //格式：BucketName-APPID
                $key = '/img_static/video_cover_'.date("Ymd").'/'.uniqid().'.jpg',
                $body = fopen($local_path, 'rb')
            );
            // 请求成功
            //print_r($result);
            $cover_cos_url='http://'.$result['Location'];
            return $cover_cos_url;

        } catch (\Exception $e) {
            // 请求失败
            return false;
        }

    }


    /**
     *Time:2020/11/23 17:19
     *Author:始渲
     *Remark:ffmpe生成视频gif图，然后上传到cos,最后返回cos地址
     * @params:$video_url 视频地址  $fromSeconds：在视频多少秒开始生成 $endSeconds:剪多少秒
     * @return:$video_cover
     */
    public static function getVideoCovergif($video_url,$fromSeconds=0,$endSeconds=2)
    {
        $savePath=Yii::getAlias('@staticroot').'/uploads/';
        $file_dir='video_cover_'.date("Ymd");
        $fileName='videogifimg_'.uniqid().'.gif';

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
            //$host='http://api.axes.com';
            //$video_url='http://axe-video-1257242485.cos.ap-guangzhou.myqcloud.com/axe_uploads/video_20201119/5fb64e250e1ad.mp4';
            //$video_url='D:\NEXT\test\xxxx.mp4';
        } else {//线上linux
            $ffmpeg_config_arr=[
                //绑定插件
                'ffmpeg.binaries'  => '/usr/local/ffmpeg/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/local/ffmpeg/bin/ffprobe',
                //'timeout'          => 3600, // The timeout for the underlying process
                //'ffmpeg.threads'   => 12,   // The number of threads that FFMpeg should use
            ];
            //$host='http://81.71.11.205:8000';
            //$video_url='/data/site/axes/api/web/uploads/video_20200924/20200924043502_5f6c5a3630ed7.mp4';
        }
        $host=Yii::getAlias('@api_host');

        $ffmpeg = \FFMpeg\FFMpeg::create(
            $ffmpeg_config_arr
        );
        $video = $ffmpeg->open($video_url);
        $video
            ->filters()
            ->resize(new \FFMpeg\Coordinate\Dimension(720, 1280))
            ->synchronize();
        //生成视频截图
        //$r=$video
        //    ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds($fromSeconds))
        //    ->save($path.'/'.$fileName);
        //生成gif图
        $r=$video
            ->gif(\FFMpeg\Coordinate\TimeCode::fromSeconds($fromSeconds), new \FFMpeg\Coordinate\Dimension(720, 720), $endSeconds)
            ->save($path.'/'.$fileName);

        if($r){//返回生成的视频封面图
//            echo $path.'/'.$fileName;
//            echo '<br />';
//            echo $host.'/uploads/'.$file_dir.'/'.$fileName;
            $video_cover=$path.'/'.$fileName;//生成的本地图片绝对地址
            $video_host_cover=$host.'/uploads/'.$file_dir.'/'.$fileName;//生成的本地图片host地址

            //上传到cos
            $video_cover_cos_url=SELF::uploadgiftocos($video_cover);
            if($video_cover_cos_url){
                return $video_cover_cos_url;
            }else{//上传到cos失败返回本地服务器的图片地址
                return $video_host_cover;
            }
        }else{
            return false;
        }

    }

    //上传本地gif图到cos，返回cos地址
    public static function uploadgiftocos($video_local_cover){

        $secretId = \Yii::$app->params['tencent_cos']['secretId']; //"云 API 密钥 SecretId";
        $secretKey = \Yii::$app->params['tencent_cos']['secretKey']; //"云 API 密钥 SecretKey";
        $region = "ap-guangzhou"; //设置一个默认的存储桶地域
        $cosClient = new \Qcloud\Cos\Client(
            array(
                'region' => $region,
                'schema' => 'http', //协议头部，默认为http
                'credentials'=> array(
                    'secretId'  => $secretId ,
                    'secretKey' => $secretKey)));
        //$local_path = "/Applications/XAMPP/web/demo/jjjj.png";
        $local_path = $video_local_cover;
        try {
            $result = $cosClient->upload(
                $bucket = 'axe-video-1257242485', //格式：BucketName-APPID
                $key = '/img_static/video_cover_gif_'.date("Ymd").'/'.uniqid().'.gif',
                $body = fopen($local_path, 'rb')
            );
            // 请求成功
            //print_r($result);
            $cover_cos_url='http://'.$result['Location'];
            return $cover_cos_url;

        } catch (\Exception $e) {
            // 请求失败
            return false;
        }

    }


}