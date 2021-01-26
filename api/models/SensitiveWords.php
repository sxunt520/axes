<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "{{%sensitive_words}}".
 *
 * @property string $id
 * @property string $word
 */
class SensitiveWords extends \common\models\SensitiveWords
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'], 'required'],
            [['word'], 'string', 'max' => 30],
            [['word'], 'unique']
        ];
    }

    function __construct()
    {
        # code...
    }


    /**
     * @todo敏感词过滤，返回结果
     * @paramarray $list 定义敏感词一维数组
     * @paramstring $string 要过滤的内容
     * @return $sensitive_msg_arr 处理结果
     *      count 敏感词数
     *      sensitiveWord 包含的敏感词组
     *      stringAfter 敏感词替换*后的字符串
     */
    protected function sensitive($list, $string)
    {

        $count = 0; //违规词的个数
        $sensitiveWord = ''; //违规词
        $stringAfter = $string; //替换后的内容
        $pattern = "/" . implode("|", $list) . "/i"; //定义正则表达式
        //echo $pattern;exit;

        if (preg_match_all($pattern, $string, $matches)) { //匹配到了结果
            $patternList = $matches[0]; //匹配到的数组
            $count = count($patternList);
            $sensitiveWord = implode(',', $patternList); //敏感词数组转字符串
            $replaceArray = array_combine($patternList, array_fill(0, count($patternList), '*')); //把匹配到的数组进行合并，替换使用
            $stringAfter = strtr($string, $replaceArray); //结果替换
        }

//        $log = "原句为 [ {$string} ]";
//        if ($count == 0) {
//            $log .= "暂未匹配到敏感词！";
//        } else {
//            $log .= "匹配到 [ {$count} ]个敏感词：[ {$sensitiveWord} ]" . "替换后为：[ {$stringAfter} ]";
//        }
//        return $log;

        $sensitive_msg_arr=array();
        $sensitive_msg_arr['count']=$count;//敏感词数
        $sensitive_msg_arr['sensitiveWord']=$sensitiveWord;//包含的敏感词组
        $sensitive_msg_arr['stringAfter']=$stringAfter;//敏感词替换*后的字符串
        return $sensitive_msg_arr;

    }

    /**
     *Time:2021/1/5 11:23
     *Author:始渲
     *Remark:过滤敏感词入口
     * @params:sensitive_str 过滤字符串
     * @return $result 处理结果
     *      count 敏感词数 >0就有敏感词
     *      sensitiveWord 包含的敏感词组
     *      stringAfter 敏感词替换*后的字符串
     */
    public static function matching_sensitive($sensitive_str)
    {
        //$sensitive_str = 'likeyou小白喜欢小黑爱着的大黄'; //要过滤的内容
        //$sensitive_wordes_list = ['18大','小明', '小红', '大白', '小白', '小黑', 'me', 'you','代办证件**']; //定义敏感词数组

        $sensitive_wordes_rows=SELF::find()->asArray()->limit(2184)->all();//库里找到敏感词类
        $sensitive_wordes_list = array_column($sensitive_wordes_rows, 'word');//转换为一维数组

        //批量转义特殊字符 preg_quote
        foreach ($sensitive_wordes_list as $k=>$v){
            $sensitive_wordes_list[$k]=preg_quote($v);
        }

        $result = SensitiveWords::sensitive($sensitive_wordes_list, $sensitive_str);
        return $result;

    }

    //单条循环匹配敏感词
    public static function matching_sensitive_one($sensitive_str)
    {
        //$sensitive_str = 'likeyou小白喜欢小黑爱着的大黄'; //要过滤的内容
        //$sensitive_wordes_list = ['18大','小明', '小红', '大白', '小白', '小黑', 'me', 'you','代办证件**']; //定义敏感词数组

        $sensitive_wordes_rows=SELF::find()->asArray()->all();//库里找到敏感词类
        $sensitive_wordes_list = array_column($sensitive_wordes_rows, 'word');//转换为一维数组

        //批量转义特殊字符 preg_quote
        foreach ($sensitive_wordes_list as $k=>$v){
            $wordes_quote=preg_quote($v);//转义
            $pattern = "/" . $wordes_quote . "/i"; //定义正则表达式
            if (@preg_match($pattern, $sensitive_str)) { //匹配到了结果
                return true;//找到了直接退出返回
            }

        }

    }

    //单条循环匹配敏感词
    public static function matching_sensitive_one2($sensitive_str)
    {
        $sensitive_wordes_rows=SELF::find()->asArray()->all();//库里找到敏感词类
        $sensitive_wordes_list = array_column($sensitive_wordes_rows, 'word');//转换为一维数组

        //批量转义特殊字符 preg_quote
        $arr=array();
        $arr['is_sensitive']=false;
        $arr['sensitive_str']='';
        foreach ($sensitive_wordes_list as $k=>$v){
            $wordes_quote=preg_quote($v);//转义
            $pattern = "/" . $wordes_quote . "/i"; //定义正则表达式
            if (@preg_match($pattern, $sensitive_str)) { //匹配到了结果
                $arr['is_sensitive']=true;
                $arr['sensitive_str']=$wordes_quote;
                return $arr;//找到了直接退出返回
            }

        }

    }


}
