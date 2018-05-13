<?php

namespace models\manage;

use app\models\manage\searchkey\PrefDistMaster;
use tests\codeception\fixtures\PrefDistMasterFixture;
use tests\codeception\unit\JmTestCase;
use yii\base\Exception;

/**
 * @group job_relations
 */
class PrefDistMasterTest extends JmTestCase
{
    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new PrefDistMaster();
        verify($model->attributeLabels())->notEmpty();
    }

    public function testRules()
    {
        $this->specify('カテゴリ名空時の検証', function() {
            $model = new PrefDistMaster();
            $model->load(['PrefDistMaster' => [
                'pref_dist_name' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_dist_name'))->true();
        });
        $this->specify('カテゴリ名最大文字数の検証', function() {
            $model = new PrefDistMaster();
            $model->load(['PrefDistMaster' => [
                'pref_dist_name' => str_repeat('1',51),
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_dist_name'))->true();
        });
        $this->specify('カテゴリ名の同一都道府県内の重複チェック', function() {
            $model = new PrefDistMaster();
            $model->pref_id = 49;
            $model->load(['PrefDistMaster' => [
                'pref_dist_name' => '同一都道府県内重複テスト',
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_dist_name'))->true();
        });
        $this->specify('カテゴリ名の同一都道府県外の重複チェック', function() {
            $model = new PrefDistMaster();
            // 青森県を指定し、北海道の既存地域グループを指定する。
            // 重複チェックに引っかからないことをチェック。
            $model->pref_id = 50;
            $model->load(['PrefDistMaster' => [
                'pref_dist_name' => '札幌市周辺',
            ]]);
            $model->validate();
            verify($model->hasErrors('pref_dist_name'))->false();
        });
        $this->specify('表示順空時の検証', function() {
            $model = new PrefDistMaster();
            $model->load(['PrefDistMaster' => [
                'sort' => '',
            ]]);
            $model->validate();
            verify($model->hasErrors('sort'))->true();
        });
        $this->specify('公開状況空時の検証', function() {
            $model = new PrefDistMaster();
            $model->load(['PrefDistMaster' => [
                'valid_chk' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('公開状況数字外の検証', function() {
            $model = new PrefDistMaster();
            $model->load(['PrefDistMaster' => [
                'valid_chk' => 'aaa',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('正しい値', function() {
            $model = new PrefDistMaster();
            $model->load(['PrefDistMaster' => [
                'pref_dist_name' => '文字列',
                'sort' => 1,
                'valid_chk' => 1,
                'pref_id' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }
}
