<?php

Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('autoheroes', dirname(dirname(__DIR__)) . '/autoheroes');
Yii::setAlias('api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('tests', dirname(dirname(__DIR__)) . '/tests');
Yii::setAlias('runnerScript', dirname(dirname(dirname(__FILE__))) .'/yii');
Yii::setAlias('staticroot', dirname(dirname(__DIR__)) . '/api/web/');
if (YII_ENV=='dev') {
    Yii::setAlias('api_host', 'http://api.axes.com');
    Yii::setAlias('static', 'http://api.axes.com');
//	Yii::setAlias('api_host', 'http://192.168.0.149:188');
//    Yii::setAlias('static', 'http://192.168.0.149:188');
} else {
	Yii::setAlias('api_host', 'http://81.71.11.205:8000');
    Yii::setAlias('static', 'http://81.71.11.205:8000');
}