<?php
namespace api\models;

use api\models\Member;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class MobileLoginForm extends Model
{
    public $username;
    public $password;
    public $mobile;

    private $_user;

    const GET_API_TOKEN = 'generate_api_token';

    public function init ()
    {
        parent::init();
        $this->on(self::GET_API_TOKEN, [$this, 'onGenerateApiToken']);//self::generate_api_token 绑定下面的Token获取 onGenerateApiToken
    }


    /**
     * @inheritdoc
     * 对客户端表单数据进行验证的rule
     */
    public function rules()
    {
//        return [
//            [['username', 'password'], 'required'],
//            ['password', 'validatePassword'],
//        ];

//        ['t_mobile', 'filter', 'filter' => 'trim'],
//        ['t_mobile', 'required'],
//         //targetClass 不会自己调用Ajax验证，提交表单后才会触发
//        ['t_mobile', 'unique', 'targetClass' => '\login\models\User', 'message' => '手机号已经注册。'],
//        [['t_mobile'],'match','pattern'=>'/^[1][358][0-9]{9}$/'],

        return [
            //['mobile', 'filter', 'filter' => 'trim'],
            [['mobile'], 'required'],
            //[['mobile'],'match','pattern'=>'/^[1][358][0-9]{9}$/'],
        ];
    }

    /**
     * 自定义的密码认证方法
     */
//    public function validatePassword($attribute, $params)
//    {
//        if (!$this->hasErrors()) {
//            $this->_user = $this->getUser();
//            if (!$this->_user || !$this->_user->validatePassword($this->password)) {
//                //$this->addError($attribute, '用户名或密码错误.');
//                $this->addErrors(['message'=>'用户名或密码错误.','status'=>-1]);
//                //$this->addError('code', -1);
//            }
//        }
//    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            //'username' => '用户名',
            //'password' => '密码',
            'mobile' => '手机号',
        ];
    }

    public function mobile_login()
    {
        if ($this->validate()) {
            if (!$this->hasErrors()) {
                $this->_user = $this->getMobileUser();
                if (!$this->_user) {
                    $this->addErrors(['message'=>'登录失败,请重新发验证码登录!','status'=>-1]);
                }
            }
            $this->trigger(self::GET_API_TOKEN);
            return $this->_user;
        } else {
            return null;
        }
    }


    /**
     * 根据手机号获取用户的认证信息
     *
     * @return User|null
     */
    protected function getMobileUser()
    {
        if ($this->_user === null) {
            $this->_user = Member::findByMobileUser($this->mobile);
        }

        return $this->_user;
    }

    /**
     * 登录校验成功后，为用户生成新的token
     * 如果token失效，则重新生成token
     */
    public function onGenerateApiToken ()
    {
        if (!Member::apiTokenIsValid($this->_user->api_token)) {
            $this->_user->generateApiToken();
            $this->_user->save(false);
        }
    }
}