<?php
/**
 * author: yidashi
 * Date: 2015/11/30
 * Time: 17:30.
 */
namespace autoheroes\behaviors;

use Detection\MobileDetect;
use yii\base\ActionFilter;

class ThemeBehavior extends ActionFilter
{
    public function beforeAction($action)
    {
        $isMobile = (new MobileDetect())->isMobile();
        $themeName = \Yii::$app->config->get('THEME_NAME', 'basic');
        $theme = [
            'class' => 'yii\base\Theme',
            'basePath' => '@autoheroes/themes/basic',
            'baseUrl' => '@web/themes/basic',
            'pathMap' => [
                '@autoheroes/views' => [
                    '@autoheroes/themes/'.($isMobile ? 'mobile' : $themeName),
                    //'@frontend/themes/'.($isMobile ? 'basic' : $themeName),
                    '@autoheroes/themes/basic',
                ],
            ],
        ];
        \Yii::$app->view->theme = \Yii::createObject($theme);
        return true;
    }
}
