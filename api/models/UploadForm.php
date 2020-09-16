<?php

namespace api\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use Imagine\Image\ManipulatorInterface;
use yii\imagine\Image;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

//    public function upload()
//    {
//        if ($this->validate()) {
//            $dir = "uploads/".date("Ymd");
//            if (!is_dir($dir)){
//                mkdir($dir);
//            }
//            $upurl=$dir."/".date("Ymd").'_'.uniqid('axe',true) . '.' . $this->imageFile->extension;
//            $r=$this->imageFile->saveAs($upurl);
//            if($r){
//                return $upurl;
//            }
//        } else {
//            return false;
//        }
//    }
    /**
     *Time:2020/9/16 13:46
     *Author:始渲
     *Remark:上传图片
     * @params:$is_thumb 是否生成缩略图 及宽、高 $img_w $img_h
     */
    public function upload($is_thumb=true,$img_w=100,$img_h=100)
    {
        if ($this->validate()) {
            $dir = "uploads/".date("Ymd");
            if (!is_dir($dir)){
                mkdir($dir);
            }
            $filename=date("Ymd").'_'.uniqid('axe',true) . '.' . $this->imageFile->extension;//文件名
            $upurl=$dir."/".$filename;//文件目录+名
            $image_save_r=$this->imageFile->saveAs($upurl);
            if($image_save_r){

                //生成缩略图
                if($is_thumb&&trim($filename) && file_exists(Yii::getAlias($upurl))) {

                    //$image = \yii\imagine\Image::thumbnail(Yii::getAlias($upurl), $img_w, $img_h, \Imagine\Image\ManipulatorInterface::THUMBNAIL_OUTBOUND);
                    $image = Image::thumbnail(Yii::getAlias($upurl), $img_w, $img_h, ManipulatorInterface::THUMBNAIL_OUTBOUND);
                    $thumb_img_url=$dir . '/thumb_' . $filename;
                    $thumb_r=$image->save($thumb_img_url);
                    if($thumb_r){
                        return $thumb_img_url;
                    }else{
                        return $upurl;
                    }

                    //裁剪
//                    $image = Image::crop(Yii::getAlias($upurl), $img_w, $img_h,[0,0]);
//                    $thumb_img_url=$dir . '/crop_' . $filename;
//                    $thumb_r=$image->save($thumb_img_url);
//                    if($thumb_r){
//                        return $thumb_img_url;
//                    }else{
//                        return $upurl;
//                    }

                }else{
                    return $upurl;
                }

            }else{
                return false;
            }
        } else {
            return false;
        }

    }


    //裁剪
    public function actionCrop()
    {
        Image::crop('11.jpg', 1000, 1000,[500,500])
            ->save('11_crop.jpg');
    }

    //旋转
    public function actionRotate()
    {
        Image::frame('11.jpg', 5, '666', 0)
            ->rotate(-8)
            ->save('11_rotate.jpg', ['quality' => 50]);

    }

    //缩略图（压缩）
    public function actionThumb()
    {
        Image::thumbnail('11.jpg', 100, 50,ManipulatorInterface::THUMBNAIL_OUTBOUND)
            ->save('11_thumb.jpg');
    }


    //图片水印
    public function actionWatermark()
    {
        Image::watermark('11.jpg', '11_thumb.jpg', [10,10])
            ->save('11_water.jpg');
    }


    //文字水印
    //字体参数 the file path or path alias (string)
    public function actionText()
    {
        Image::text('11.jpg', 'hello world', 'glyphicons-halflings-regular.ttf',[10,10],[])
            ->save('11_text.jpg');
    }


}