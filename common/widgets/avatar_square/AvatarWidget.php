<?php
namespace common\widgets\avatar_square;
/**
 * 设置挂件
 * @author 上班偷偷打酱油 （xianan_huang@163.com）
 * @copyright Yii中文网 （www.yii-china.com）
 */
use Yii;
use yii\bootstrap\Widget;
use common\widgets\avatar_square\assets\AvatarAsset;
use yii\base\Object;

class AvatarWidget extends Widget
{    
    public $imageUrl = '';
    
    public function run()
    {
        $this->registerClientScript();
        $model = new UploadForm();        
        return $this->render('index',['model'=>$model]);
    }
    
    public function registerClientScript()
    {
        AvatarAsset::register($this->view);
        //$script = "FormFileUpload.init();";
        //$this->view->registerJs($script, View::POS_READY);
    }
}