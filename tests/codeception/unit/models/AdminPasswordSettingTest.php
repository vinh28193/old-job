<?php
/**
 * Created by PhpStorm.
 * User: n.katayama
 * Date: 2016/11/02
 * Time: 12:04
 */

namespace models\manage;


use app\models\AdminPasswordSetting;
use tests\codeception\unit\JmTestCase;

class AdminPasswordSettingTest extends JmTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testRules()
    {
        // passwordRepeat required
        $model = new AdminPasswordSetting();
        $model->validate();
        verify($model->hasErrors('passwordRepeat'))->true();

        // passwordRepeat match
        $model = new AdminPasswordSetting();
        $model->load(['AdminPasswordSetting' => [
            'password' => 'aaa@aaa',
            'passwordRepeat' => 'aaa@aaa'
        ]]);
        $model->validate();
        verify($model->hasErrors('passwordRepeat'))->true();

        // passwordRepeat string min
        $model = new AdminPasswordSetting();
        $model->load(['AdminPasswordSetting' => [
            'password' => 'aaa',
            'passwordRepeat' => 'aaa'
        ]]);
        $model->validate();
        verify($model->hasErrors('passwordRepeat'))->true();

        // passwordRepeat compare
        $model = new AdminPasswordSetting();
        $model->load(['AdminPasswordSetting' => [
            'password' => 'aaaaa',
            'passwordRepeat' => 'bbbbbb'
        ]]);
        $model->validate();
        verify($model->hasErrors('passwordRepeat'))->true();

        // model load true
        $model = new AdminPasswordSetting();
        $model->load(['AdminPasswordSetting' => [
            'password' => 'aaaaaa',
            'passwordRepeat' => 'aaaaaa'
        ]]);
        $model->validate();
        verify($model->hasErrors('passwordRepeat'))->false();
    }

    // 不要
//    public function testAttributeLabels(){}
//    public function testGetPasswordReminder(){}
}
