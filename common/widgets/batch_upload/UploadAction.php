<?php
namespace common\widgets\batch_upload;
/**
 * @author Aaron Zhanglong <815818648@qq.com>
 * 多图上传组件
 * @Date: 2017-07-14
 * @usage :
 * 模板页面： <?=$form->field($model, 'pics')->widget('common\widgets\batch_upload\FileUpload')?>
 *
 * 上传图片控制器脚本：
 *
 * public function actions() {
        return [
            'upload_more'=>[
                'class' => 'common\widgets\batch_upload\UploadAction'
            ]
        ];
    }
 */

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use common\widgets\batch_upload\Uploader;
use common\models\GoodsPictures;

class UploadAction extends Action
{
    /**
     * 配置项
     */
    public $config = [];

    public function init() {
        //close csrf
        Yii::$app->request->enableCsrfValidation = false;
        //默认设置
        $_config = require(__DIR__ . '/config.php');
        $this->config = ArrayHelper::merge($_config, $this->config);
        parent::init();
    }

    public function run() {
        $action = Yii::$app->request->get('action');
        $img_w = Yii::$app->request->post('img_w');
        $img_h = Yii::$app->request->post('img_h');
        if($action == 'delete'){
            $pic = Yii::$app->request->get('pic');
            $pic = $this->config['uploadFilePath'] . $pic;

            $picture_id=Yii::$app->request->get('picture_id');

            if($picture_id==-1){
                
                if(file_exists($pic)){
                    if($this->config['trueDelete']){
                        @unlink($pic);
                        echo 'ok';exit;
                    }
                }

            }elseif ($picture_id>0) {
                
                $r=GoodsPictures::findOne($picture_id)->delete();
                if($r){
                    if(file_exists($pic)){
                        if($this->config['trueDelete']){
                            @unlink($pic);
                        }
                    }
                    echo 'ok';exit;
                }else{
                    echo '删除图片失败';exit;
                }

            }else{
                echo '删除图片失败';exit;
            }

            // $r=GoodsPictures::findOne($picture_id)->delete();

            // if($r){
            //     $pic = $this->config['uploadFilePath'] . $pic;
            //     if(file_exists($pic)){
            //         if($this->config['trueDelete']){
            //             @unlink($pic);
            //         }
            //     }
            //     echo 'ok';exit;
            // }else{
            //     echo '删除图片失败';exit;
            // }

        }else{
            $result = $this->ActUpload($img_w,$img_h);
            echo $result;exit;
        }
    }

    /**
     * 上传
     * @return string
     */
    protected function ActUpload($img_w,$img_h) {
        //上传类型
        $upload_type = $this->config['uploadType'];
        //上传路径
        $this->config['uploadFilePath'] = isset($this->config['uploadFilePath']) ? $this->config['uploadFilePath'] : '';
        //文件数组下标
        $fieldName = $this->config['fieldName'];
        /* 生成上传实例对象并完成上传 */
        $up = new Uploader($fieldName, $this->config, $upload_type);

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
        $uparr=$up->getFileInfo();

        $dir=str_replace('backend', 'app', $_SERVER['DOCUMENT_ROOT']);
        $file_url=$uparr['url'];
        $filename=$uparr['title'];
        $img_all_url=$dir.$file_url;
        $dirname=dirname($img_all_url);//图片本地地址目录 
        $host_dir=dirname($file_url);//服务器地址目录
        
        //生成缩略图
        if(trim($filename) && file_exists(Yii::getAlias($img_all_url))) {
        
            $image = \yii\imagine\Image::thumbnail(Yii::getAlias($img_all_url), $img_w, $img_h, \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND);
            $r=$image->save($dirname . '/thumb-' . $filename);
            if($r){
                    //echo  'small-' . $filename;
                $uparr['thumb_url']=$host_dir . '/thumb-' . $filename;
            }
            
        }
        //var_dump($up);exit;
        //echo $up['url'];exit;
        
        /* 返回数据 */
        //return json_encode($up->getFileInfo());
        return json_encode($uparr);
    }
}