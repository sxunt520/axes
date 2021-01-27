<?php
namespace common\widgets\avatar_square;

use Yii;
use yii\base\Model;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;
    
    public $avatarData;
    
    public $config;
    
    public $imageUrl;

    public $_lastError;
    
    public function rules()
    {
        return [
            [['avatarData','config'], 'required'],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg, gif'],
        ];
    }

    /**
     * 图片上传
     * @throws \Exception
     * @return boolean
     */
    public function upload()
    {
        try{
            $userId = isset(Yii::$app->user->identity->id)?Yii::$app->user->identity->id:"000000";
            $file_dir=date("Ymd");
            //$path = $this->config['uploadPath'].'/'.$userId;
            $path = $this->config['uploadPath'].'/'.$file_dir;
    
            if(!$this->mkDirs($path)){
                throw new \Exception('上传目录生成失败！');
            }
            
            //图片裁剪
            $bigImage = $this->crop();
            //配置大中小图上传路径

            $file_name=uniqid() . '_thumb'.'.'.$this->imageFile->extension;
            $bigImageUrl = $path.'/'.$file_name;
            //$middleImageUrl = $path.'/'.date("YmdHis") . '_middle'.'.'.$this->imageFile->extension;
            //$smallImageUrl = $path.'/'.date("YmdHis") . '_small'.'.'.$this->imageFile->extension;
           
            //生成缩略中小图
            //$middleImage = imagecreatetruecolor($this->config['middleImageWidth'], $this->config['middleImageHeight']);
            //$smallImage = imagecreatetruecolor($this->config['smallImageWidth'], $this->config['smallImageHeight']);            
            //imagecopyresampled($middleImage, $bigImage, 0, 0, 0, 0, $this->config['middleImageWidth'], $this->config['middleImageHeight'], $this->config['bigImageWidth'], $this->config['bigImageHeight']);
            // imagecopyresampled($smallImage, $bigImage, 0, 0, 0, 0, $this->config['smallImageWidth'], $this->config['smallImageHeight'], $this->config['bigImageWidth'], $this->config['bigImageHeight']);
            
            //图片移动到本地对应目录
            //if (!imagepng($bigImage, $bigImageUrl)||!imagepng($middleImage, $middleImageUrl) ||!imagepng($smallImage, $smallImageUrl)) {
            //if (!imagepng($bigImage, $bigImageUrl,9)) {
            if (!imagejpeg($bigImage, $bigImageUrl,80)) {
                throw new \Exception('上传失败！');
            }
            
            //本地地址： $this->imageUrl = Yii::getAlias('@static').'/uploads/'.$file_dir.'/'.$file_name;

            //////////////////上传到cos////////////////
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
                $local_path = $bigImageUrl;
                try {
                    $result = $cosClient->upload(
                        $bucket = 'axe-video-1257242485', //格式：BucketName-APPID
                        $key = '/img_static/'.$file_dir.'/'.$file_name,
                        $body = fopen($local_path, 'rb')
                    );
                    // 请求成功
                    //print_r($result);
                    $this->imageUrl = 'http://'.$result['Location'];
                    return true;
                } catch (\Exception $e) {
                    // 请求失败
                    echo($e);
                    return false;
                }
            //////////////////上传到cos////////////////

        }catch (\Exception $e){
            $this->_lastError = $e->getMessage();
            return false;
        }

    }
    
    private function mkDirs($dir){
        if(!is_dir($dir)){

            if(!$this->mkDirs(dirname($dir))){
                return false;
            }
            if(!mkdir($dir,0777)){
                return false;
            }
        }
        return true;
    }
    
    /**
 * bool imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
 * $dst_image：新建的图片
 * $src_image：需要载入的图片
 * $dst_x：设定需要载入的图片在新图中的x坐标
 * $dst_y：设定需要载入的图片在新图中的y坐标
 * $src_x：设定载入图片要载入的区域x坐标
 * $src_y：设定载入图片要载入的区域y坐标
 * $dst_w：设定载入的原图的宽度（在此设置缩放）
 * $dst_h：设定载入的原图的高度（在此设置缩放）
 * $src_w：原图要载入的宽度
 * $src_h：原图要载入的高度
 */
    
    public function crop()
    {     
        $data = json_decode($this->avatarData);
        $size = getimagesize($this->imageFile->tempName);
        
        switch ($size['mime']) {
            case 'image/gif':
                $src_img = imagecreatefromgif($this->imageFile->tempName);
                break;
        
            case 'image/jpeg':
                $src_img = imagecreatefromjpeg($this->imageFile->tempName);
                break;
            
            case 'image/jpg':$dm = imagecreatefromjpeg($this->imageFile->tempName);
                break;
        
            case 'image/png':
                $src_img = imagecreatefrompng($this->imageFile->tempName);
                break;
        }
        
                
        $size_w = $size[0]; // natural width
        $size_h = $size[1]; // natural height
        
        $src_img_w = $size_w;
        $src_img_h = $size_h;
        
        $tmp_img_w = $data -> width;
        $tmp_img_h = $data -> height;
        $dst_img_w = $this->config['bigImageWidth'];
        $dst_img_h = $this->config['bigImageHeight'];;
        
        $src_x = $data -> x;
        $src_y = $data -> y;
        
        if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
            $src_x = $src_w = $dst_x = $dst_w = 0;
        } else if ($src_x <= 0) {
            $dst_x = -$src_x;
            $src_x = 0;
            $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
        } else if ($src_x <= $src_img_w) {
            $dst_x = 0;
            $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
        }
        
        if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
            $src_y = $src_h = $dst_y = $dst_h = 0;
        } else if ($src_y <= 0) {
            $dst_y = -$src_y;
            $src_y = 0;
            $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
        } else if ($src_y <= $src_img_h) {
            $dst_y = 0;
            $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
        }
        
        $ratio = $tmp_img_w / $dst_img_w;
        $dst_x /= $ratio;
        $dst_y /= $ratio;
        $dst_w /= $ratio;
        $dst_h /= $ratio;
        
        $dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);
        imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 127));
        imagesavealpha($dst_img, true);
        $result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);        
        
        if ($result) {
            return $dst_img;
        } else {
            return null;
        }
    }
}
