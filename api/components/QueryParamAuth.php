<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

//namespace yii\filters\auth;
namespace api\components;
use Yii;
use yii\filters\auth\AuthMethod;

/**
 * QueryParamAuth is an action filter that supports the authentication based on the access token passed through a query parameter.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class QueryParamAuth extends AuthMethod
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access-token';


    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        //$accessToken = $request->get($this->tokenParam);
        
        //报头里去获取token
        $headers = Yii::$app->getRequest()->getHeaders();
        $accessToken = $headers->get('token');
        
        if (is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}
