<?php
/**
 * Created by PhpStorm.
 * User: yidashi
 * Date: 16/4/8
 * Time: 下午5:43
 */

namespace common\widgets\tag;

use yii\helpers\Html;
use yii\widgets\InputWidget;

class Tag extends InputWidget
{
    public $id = 'tags';
    public function init()
    {
        $this->options['id'] = $this->id;
    }
    public function run()
    {
        TagAsset::register($this->view);

        $this->view->registerJs(<<<JS
$('#{$this->id}').tagsInput({
   'height':'100px',
   'width':'300px',
   'interactive':true,
   'defaultText':'',
   'delimiter': ' ',   // Or a string with a single delimiter. Ex: ';'
   'removeWithBackspace' : true,
   'minChars' : 0,
   'maxChars' : 0, // if not provided there is no limit
   'placeholderColor' : '#666666'
});
JS
);
        if($this->hasModel()){
            return Html::activeTextInput($this->model,$this->attribute,$this->options);
        }else{
            return Html::textInput($this->name,$this->value,$this->options);
        }
    }
}