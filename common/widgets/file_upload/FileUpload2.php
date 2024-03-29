<?php
/**
 * @see Yii中文网  http://www.yii-china.com
 * @author Xianan Huang <Xianan_huang@163.com>
 * 图片上传组件
 * 如何配置请到官网（Yii中文网）查看相关文章
 */
namespace common\widgets\file_upload;

use Yii;
use yii\widgets\InputWidget;
use yii\helpers\Html;
use yii\web\View;
use common\widgets\file_upload\assets\FileUploadAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class FileUpload2 extends InputWidget
{
    public $config = [];
    
    public $value = '';
    
    public function init()
    {
        $_config = [
            'serverUrl' => Url::to(['upload_one','action'=>'uploadimage','adv_width'=>$this->config['adv_width'],'adv_height'=>$this->config['adv_height']]),  //上传服务器地址
            'fileName' => 'upfile',                                      //提交的图片表单名称 
            //'domain_url' => \Yii::$app->params['images'],                   //图片域名 不填为当前域名
            'domain_url' => Yii::getAlias('@static'),
        ];
        $this->config = ArrayHelper::merge($_config, $this->config);
    }
    
    public function run()
    {
        $this->registerClientScript();        
        if ($this->hasModel()) {
            $inputName = Html::getInputName($this->model, $this->attribute);
            $inputValue = Html::getAttributeValue($this->model, $this->attribute);
            return $this->render('index2',[
                'config'=>$this->config,
                'inputName' => $inputName,
                'inputValue' => $inputValue,
                'attribute' => $this->attribute,
            ]);
        } else {
            return $this->render('index2',[
                'config'=>$this->config,
                'inputName' => $this->config['inputName'],
                'inputValue'=> $this->value,
                'attribute' => uniqid(),
            ]);
        }
    }
    
    public function registerClientScript()
    {
        FileUploadAsset::register($this->view);
        //$script = "FormFileUpload.init();";
        //$this->view->registerJs($script, View::POS_READY);
    }
}