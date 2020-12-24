<?php
namespace common\widgets\file_upload;
/**
 * @see Yii中文网  http://www.yii-china.com
 * @author Xianan Huang <Xianan_huang@163.com>
 * 图片上传组件
 * 如何配置请到官网（Yii中文网）查看相关文章
 */
 
use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use common\widgets\file_upload\Uploader;

class UploadAction extends Action
{
    /**
     * 配置文件
     * @var array
     */
    public $config = [];
    
    public function init()
    {
        //close csrf
        Yii::$app->request->enableCsrfValidation = false;
        //默认设置
        $_config = require(__DIR__ . '/config.php');
        //load config file
        $this->config = ArrayHelper::merge($_config, $this->config);
        parent::init();
    }
    
    public function run()
    {
        $action = Yii::$app->request->get('action');
        switch ($action) {
                /* 上传图片 */
            case 'uploadimage':
                /* 上传文件 */
            case 'uploadfile':
                $result = $this->ActUpload();
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }
        echo $result;
    }
    
    /**
     * 上传
     * @return string
     */
    protected function ActUpload()
    {
        $base64 = "upload";
        switch (htmlspecialchars($_GET['action'])) {
            
            case 'uploadimage':
                $config = array(
                "pathFormat" => $this->config['imagePathFormat'],
                "maxSize" => $this->config['imageMaxSize'],
                "allowFiles" => $this->config['imageAllowFiles'],
                );
                $fieldName = $this->config['imageFieldName'];
                break;
                
            case 'uploadfile':
            default:
                $config = array(
                "pathFormat" => $this->config['filePathFormat'],
                "maxSize" => $this->config['fileMaxSize'],
                "allowFiles" => $this->config['fileAllowFiles']
                );
                $fieldName = $this->config['fileFieldName'];
                break;
        }
        $config['uploadFilePath'] = isset($this->config['uploadFilePath'])?$this->config['uploadFilePath']:'';
        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $config, $base64);
        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
        */
        /* 返回数据 */
        //return json_encode($up->getFileInfo());

        $uparr=$up->getFileInfo();


        if($uparr['state']=='SUCCESS'&&$_GET['is_thumb']&&$_GET['adv_width']>0&&$_GET['adv_height']>0){//是否生成缩略图

            //$dir=str_replace('backend', 'app', $_SERVER['DOCUMENT_ROOT']);
            $dir=Yii::getAlias('@staticroot');
            $file_url=$uparr['url'];
            $filename=$uparr['title'];
            $img_all_url=$dir.$file_url;
            $dirname=dirname($img_all_url);//图片本地地址目录
            $host_dir=dirname($file_url);//服务器地址目录
            //生成缩略图
            if(trim($filename) && file_exists(Yii::getAlias($img_all_url))) {

                $image = \yii\imagine\Image::thumbnail(Yii::getAlias($img_all_url), $img_w=$_GET['adv_width'], $img_h=$_GET['adv_height'], \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND);
                $r=$image->save($dirname . '/thumb-' . $filename);
                if($r){
                    //echo  'small-' . $filename;
                    $uparr['thumb_url']=$host_dir . '/thumb-' . $filename;
                }

            }

            $uparr['is_thumb']=true;
        }else{
            $uparr['is_thumb']=false;
        }


        $is_to_cos=true;//是否上传到cos
        if($is_to_cos&&$uparr['state']=='SUCCESS'){//上传到cos

                if($uparr['is_thumb']){//是否有生成缩略图
                    $img_local_url=$uparr['thumb_url'];
                }else{
                    $img_local_url=$uparr['url'];
                }

                $local_img_all_url=Yii::getAlias('@staticroot').$img_local_url;//本地文件路径
        
                //判断本地上传的图片是否存在
                if(file_exists($local_img_all_url)) {

                        //////////////////上传到cos////////////////
                        $x_file_dir=date("Ymd");
                        $x_file_name=uniqid().$uparr['type'];
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
                        $local_path = $local_img_all_url;
                        try {
                            $result = $cosClient->upload(
                                $bucket = 'axe-video-1257242485', //格式：BucketName-APPID
                                $key = '/img_static/'.$x_file_dir.'/'.$x_file_name,
                                $body = fopen($local_path, 'rb')
                            );
                            // 请求成功
                            //print_r($result);
                            $uparr['cos_url'] = 'http://'.$result['Location'];
                            $uparr['is_to_cos']=true;
                        } catch (\Exception $e) {
                            // 请求失败
                            echo($e);
                            return false;
                        }
                    //////////////////上传到cos////////////////
                 }
        }else{
                $uparr['is_to_cos']=false;
        }


        /* 返回数据 */
        //return json_encode($up->getFileInfo());
        return json_encode($uparr);


    }
}