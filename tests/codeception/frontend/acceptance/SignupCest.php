<?php

namespace tests\codeception\frontend\acceptance;

use tests\codeception\frontend\_pages\SignupPage;
use common\models\User;

class SignupCest
{
    /**
     * This method is called before each cest class test method.
     *
     * @param \Codeception\Event\TestEvent $event
     */
    public function _before($event)
    {
    }

    /**
     * This method is called after each cest class test method, even if test failed.
     *
     * @param \Codeception\Event\TestEvent $event
     */
    public function _after($event)
    {
        User::deleteAll([
            'email' => 'tester.email@example.com',
            'username' => 'tester',
        ]);
    }

    /**
     * This method is called when test fails.
     *
     * @param \Codeception\Event\FailEvent $event
     */
    public function _fail($event)
    {
    }

    /**
     * @param \codeception_frontend\AcceptanceTester $I
     * @param \Codeception\Scenario                  $scenario
     */
    public function testUserSignup($I, $scenario)
    {
        $I->wantTo('ensure that signup works');

        $signupPage = SignupPage::openBy($I);
        $I->see('注册', 'h1');
//        $I->see('Please fill out the following fields to signup:');

        $I->amGoingTo('submit signup form with no data');

        $signupPage->submit([]);

        $I->expectTo('see validation errors');
        $I->see('用户名不能为空。', '.help-block');
        $I->see('邮箱不能为空。', '.help-block');
        $I->see('密码不能为空。', '.help-block');

        $I->amGoingTo('submit signup form with not correct email');
        $signupPage->submit([
            'username' => 'tester',
            'email' => 'tester.email',
            'password' => 'tester_password',
        ]);

        $I->expectTo('see that email address is wrong');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->see('邮箱不是有效的邮箱地址。', '.help-block');

        $I->amGoingTo('submit signup form with correct email');
        $signupPage->submit([
            'username' => 'tester',
            'email' => 'tester.email@example.com',
            'password' => 'tester_password',
        ]);

        $I->expectTo('see that user logged in');
        $I->seeLink('退出 (tester)');
    }
}
