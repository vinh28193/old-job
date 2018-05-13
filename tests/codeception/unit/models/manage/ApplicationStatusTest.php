<?php

namespace models\manage;

use app\models\manage\ApplicationStatus;
use tests\codeception\fixtures\ApplicationStatusFixture;

use tests\codeception\unit\JmTestCase;
use yii\base\Exception;

use yii;

class ApplicationStatusTest extends JmTestCase
{
    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new ApplicationStatus();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('状況コード空時の検証', function() {
            $model = new ApplicationStatus();
            $model->load(['ApplicationStatus' => [
                'application_status_no' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('application_status_no'))->true();
        });
        $this->specify('状況コードの数字外の検証', function() {
            $model = new ApplicationStatus();
            $model->load(['ApplicationStatus' => [
                'application_status_no' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('application_status_no'))->true();
        });
        $this->specify('状況空時の検証', function() {
            $model = new ApplicationStatus();
            $model->load(['ApplicationStatus' => [
                'application_status' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('application_status'))->true();
        });
        $this->specify('カテゴリ名最大文字数の検証', function() {
            $model = new ApplicationStatus();
            $model->load(['ApplicationStatus' => [
                'application_status' => str_repeat('1',256),
            ]]);
            $model->validate();
            verify($model->hasErrors('application_status'))->true();
        });
        $this->specify('状態空時の検証', function() {
            $model = new ApplicationStatus();
            $model->load(['ApplicationStatus' => [
                'valid_chk' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('状態の数字外の検証', function() {
            $model = new ApplicationStatus();
            $model->load(['ApplicationStatus' => [
                'valid_chk' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('正しい値', function() {
            $model = new ApplicationStatus();
            $model->load(['ApplicationStatus' => [
                'application_status_no' => 1,
                'application_status' => '文字列',
                'valid_chk' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

    public function testDropDownList()
    {
        $DropDownList = ApplicationStatus::getDropDownList();
        foreach ($DropDownList as $id => $name) {
            $target = ApplicationStatus::findOne($id);
            //fixtureのカテゴリ名とfunctionから取得したカテゴリ名を比較
            verify($target->application_status)->equals($name);
            verify($target->valid_chk)->equals(1);
        }
    }

}