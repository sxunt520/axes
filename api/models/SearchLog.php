<?php

namespace api\models;

use Yii;
use api\models\SearchKeyword;

/**
 * This is the model class for table "{{%search_log}}".
 *
 * @property string $id
 * @property string $keyword
 * @property string $user_id
 * @property string $created_at
 */
class SearchLog extends \common\models\SearchLog
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
            [['keyword'], 'string', 'max' => 30]
        ];
    }

    public static function search_log($keyword='',$uesr_id=0){
        $SearchKeyword_model=SearchKeyword::find()->where(['keyword' => $keyword])->one();
        //用户个人的搜索日志记录
        $_SearchLog_model=new SearchLog();
        $_SearchLog_model->keyword=$keyword;
        $_SearchLog_model->user_id=$uesr_id;
        $_SearchLog_model->created_at=time();
        $user_isValid = $_SearchLog_model->validate();
        if($SearchKeyword_model){//先看是否已经有此关键词的记录
            if($user_isValid){
                //开启事务
                $transaction=Yii::$app->db->beginTransaction();
                try{
                    $_SearchLog_model->save(false);//用户个人关键词搜索记录存入
                    //锁定行,更新关键词搜索次数
                    $sql="select total_num from {{%search_keyword}} where keyword='{$keyword}' for update";
                    $data=Yii::$app->db->createCommand($sql)->query()->read();
                    $sql="update {{%search_keyword}} set total_num=total_num+1 where keyword='{$keyword}'";
                    Yii::$app->db->createCommand($sql)->execute();
                    $transaction->commit();//提交
                }catch (Exception $e){
                    $transaction->rollBack();//回滚
                }
            }
        }else{//如果没有，则添加
            //关键词搜索记录添加
            $_SearchKeyword_model=new SearchKeyword();
            $_SearchKeyword_model->keyword=$keyword;
            $_SearchKeyword_model->total_num=1;
            $isValid = $_SearchKeyword_model->validate();
            if($isValid&&$user_isValid){
                //开启事务
                $transaction=Yii::$app->db->beginTransaction();
                try{
                    $_SearchKeyword_model->save(false);//关键词存入
                    $_SearchLog_model->save(false);//用户个人关键词搜索记录存入
                    $transaction->commit();//提交
                }catch (Exception $e){
                    $transaction->rollBack();//回滚
                }
            }

        }
    }

}
