<?php

namespace api\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

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

    public function upload()
    {
        if ($this->validate()) {
            $dir = "uploads/".date("Ymd");
            if (!is_dir($dir)){
                mkdir($dir);
            }
            $upurl=$dir."/".date("Ymd").'_'.uniqid('axe',true) . '.' . $this->imageFile->extension;
            $r=$this->imageFile->saveAs($upurl);
            if($r){
                return $upurl;
            }
        } else {
            return false;
        }
    }
}