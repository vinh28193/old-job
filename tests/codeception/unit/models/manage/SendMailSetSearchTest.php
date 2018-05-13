<?php
namespace models\manage;

use app\models\manage\SendMailSetSearch;
use tests\codeception\unit\JmTestCase;
use tests\codeception\fixtures\SendMailSetFixture;
use yii\helpers\ArrayHelper;

/**
 * Class SendMailSetSearchTest
 * @package models\manage
 *
 * @property SendMailSetFixture $send_mail_set
 */
class SendMailSetSearchTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new SendMailSetSearch();
            $model->load([$model->formName() => [
                'mail_to' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('mail_to'))->true();
        });
        $this->specify('文字列チェック', function () {
            $model = new SendMailSetSearch();
            $model->load([$model->formName() => [
                'mail_name' => (int)1,
            ]]);
            $model->validate();
            verify($model->hasErrors('mail_name'))->true();
        });
        $this->specify('正しいチェック', function () {
            $model = new SendMailSetSearch();
            $model->load([$model->formName() => [
                'mail_to' => 1,
                'mail_name' => 'test',
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索テスト
     */
    public function testSearch()
    {
        $this->specify('キーワード(all）で検索', function () {
            $params = [
                'searchItem' => 'all',
                'searchText' => 'JOB'
            ];
            $models = $this->getSendMailSetSearch($params);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                $fromName = ArrayHelper::getValue($model, 'from_name');
                $fromAddress = ArrayHelper::getValue($model, 'from_address');
                $subject = ArrayHelper::getValue($model, 'subject');
                $contents = ArrayHelper::getValue($model, 'contents');
                verify(
                    strpos($fromName, 'JOB') !== false
                    || strpos($fromAddress, 'JOB') !== false
                    || strpos($subject, 'JOB') !== false
                    || strpos($contents, 'JOB') !== false
                )->true();
            }
        });

        $this->specify('キーワード(contents）で検索', function () {
            $params = [
                'searchItem' => 'contents',
                'searchText' => 'メール'
            ];
            $models = $this->getSendMailSetSearch($params);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'contents'))->contains('メール');
            }
        });

        $this->specify('メール種別で検索', function () {
            $params = [
                'searchItem' => 'all',
                'mail_name' => '会員登録通知'
            ];
            $models = $this->getSendMailSetSearch($params);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'mail_name'))->equals('会員登録通知');
            }
        });

        $this->specify('対象者で検索', function () {
            $params = [
                'searchItem' => 'all',
                'mail_to' => SendMailSetSearch::MAIL_TO_APPLICATION
            ];
            $models = $this->getSendMailSetSearch($params);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'mail_to'))->equals(SendMailSetSearch::MAIL_TO_APPLICATION);
            }
        });
    }

    /**
     * @param $searchParam
     * @return array
     */
    private function getSendMailSetSearch($searchParam)
    {
        $model = new SendMailSetSearch();
        $dataProvider = $model->search(['SendMailSetSearch' => $searchParam]);

        return $dataProvider->query->all();
    }

}