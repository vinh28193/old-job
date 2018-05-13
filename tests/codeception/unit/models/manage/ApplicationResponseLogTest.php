<?php

namespace models\manage;

use app\models\manage\ApplicationResponseLog;
use tests\codeception\unit\fixtures\ApplicationResponseLogFixture;
use tests\codeception\unit\fixtures\JobTypeCategoryFixture;
use tests\codeception\unit\JmTestCase;

class ApplicationResponseLogTest extends JmTestCase
{
    /**
     * フィクスチャ設定
     * @return array
     */
    public function fixtures()
    {
        return array_merge(parent::fixtures(), [
            // メイン
            'application_response_log' => ApplicationResponseLogFixture::className(),
        ]);
    }

    /**
     * attribute labels test
     */
    public function testAttributeLabels()
    {
        $model = new ApplicationResponseLog();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->setIdentity('owner_admin');
        $this->specify('応募ID空時の検証', function() {
            $model = new ApplicationResponseLog();
            $model->validate();
            verify($model->hasErrors('application_id'))->true();
        });
        $this->specify('応募IDの数字外の検証', function() {
            $model = new ApplicationResponseLog();
            $model->load(['ApplicationResponseLog' => [
                'application_id' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('application_id'))->true();
        });
        $this->specify('管理者ID空時の検証', function() {
            $model = new ApplicationResponseLog();
            $model->validate();
            verify($model->admin_id)->equals(\Yii::$app->user->id);
        });
        $this->specify('管理者IDの数字外の検証', function() {
            $model = new ApplicationResponseLog();
            $model->load(['ApplicationResponseLog' => [
                'admin_id' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('admin_id'))->true();
        });
        $this->specify('正しい値', function() {
            $model = new ApplicationResponseLog();
            $model->load(['ApplicationResponseLog' => [
                'application_id' => 1,
                'admin_id' => 1,
                'application_status_id' => 1,
                'mail_send_id' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

}