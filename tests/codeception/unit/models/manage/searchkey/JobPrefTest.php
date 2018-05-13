<?php

namespace models\manage;

use app\models\manage\searchkey\JobPref;
use Codeception\Specify;
use tests\codeception\unit\JmTestCase;
use tests\codeception\unit\fixtures\JobPrefFixture;

/**
 * @group job_relations
 */
class JobPrefTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new JobPref();
        verify(is_array($model->attributeLabels()))->true();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new JobPref();
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('pref_id'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new JobPref();
            $model->load([
                'job_master_id' => '文字列',
                'pref_id' => '文字列',
            ], '');
            $model->validate();
            verify($model->hasErrors('job_master_id'))->true();
            verify($model->hasErrors('pref_id'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new JobPref();
            $model->load([
                'job_master_id' => 10000,
                'pref_id' => 10000,
            ], '');
            verify($model->validate())->true();
        });
    }

    /**
     * 都道府県有効チェック
     */
    public function testCheckPrefArea()
    {
        $this->specify('都道府県の有効チェック 無効確認', function () {
            $model = new JobPref();
            $model->load([
                'pref_id' => 53,
            ], '');
            verify($model->checkPrefArea())->false();
        });
        $this->specify('都道府県の有効チェック 有効確認', function () {
            $model = new JobPref();
            $model->load([
                'pref_id' => 49,
            ], '');
            verify($model->checkPrefArea())->true();
        });

    }
}