<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%sensitive_keywords}}".
 *
 * @property string $id
 * @property string $word
 */
class SensitiveKeywords extends \common\models\SensitiveKeywords
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'], 'string']
        ];
    }

    //单条匹配敏感词文本
    public static function matching_sensitive_text($sensitive_str)
    {
        $sensitive_wordes_rows=SELF::find()->asArray()->all();//库里找到敏感词类
        //$sensitive_wordes_list = array_column($sensitive_wordes_rows, 'word');//转换为一维数组
        //var_dump($sensitive_wordes_list);exit;
        //批量转义特殊字符 preg_quote
        $arr=array();
        $arr['is_sensitive']=false;
        $arr['sensitive_str']='';
        //$sensitive_str=preg_quote($sensitive_str);
        foreach ($sensitive_wordes_rows as $k=>$v){
            $wordes_quote=$v['word'];
            $pattern = "/" . $wordes_quote . "/i"; //定义正则表达式
            if (@preg_match($pattern, $sensitive_str, $matches)) { //匹配到了结果
                //echo $pattern;exit;
                $matches_word_str = $matches[0]; //匹配到的敏感词
                $arr['is_sensitive']=true;
                $arr['sensitive_str']=$matches_word_str;
                return $arr;//找到了直接退出返回
            }

        }

    }

}
