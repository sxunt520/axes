<?php
/**
 * author: yidashi
 * Date: 2015/12/28
 * Time: 17:34.
 */
namespace common\behaviors;

use common\models\Article;
use yii\base\Behavior;
use yii\helpers\Url;

class PushBehavior extends Behavior
{
    public function events()
    {
        if (YII_ENV_PROD) {
            return [
                Article::EVENT_AFTER_INSERT => [$this, 'pushBaidu'],
            ];
        }

        return [];
    }

    /**
     * 主动推送给百度链接.
     *
     * @param $event
     */
    public function pushBaidu($event)
    {
        $urls = array(
//            Url::to(['/article/view', 'id' => $event->sender->getPrimaryKey()],true),
            'http://www.sxunt.com/'.$event->sender->getPrimaryKey(),
        );
        $api = 'http://data.zz.baidu.com/urls?site=www.sxunt.com&token=qm04kFWOTu8K7pEA';
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        echo $result;
    }
}
