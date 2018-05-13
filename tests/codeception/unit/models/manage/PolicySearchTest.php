<?php
namespace models\manage;

use app\models\manage\PolicySearch;
use tests\codeception\unit\JmTestCase;
use tests\codeception\fixtures\PolicyFixture;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class PolicySearchTest
 * @package models\manage
 *
 * @property PolicyFixture $policy
 */
class PolicySearchTest extends JmTestCase
{
    public function testRules()
    {
        $this->specify('文字列チェック', function () {
            $model = new PolicySearch();
            $model->load([$model->formName() => [
                'policy' => (int)1, // rule ['policy','string']
            ]]);
            $model->validate();
            verify($model->hasErrors('policy'))->true();
        });
        $this->specify('正しい値', function () {
            $model = new PolicySearch();
            $model->load([$model->formName() => [
                'policy' => 'あああ',
            ]]);
            verify($model->validate())->true();
        });
    }

    public function testSearch()
    {
        // tenantのfixtureレコード取得
        $tenantRecords = array_filter(self::getFixtureInstance('policy')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        $tenantRecords = array_values($tenantRecords);
        // 検索文字列取得
        $policy = ArrayHelper::getValue($tenantRecords[2], 'policy');
        $searchText = mb_substr($policy, 1, 20);
        // 検証
        $models = $this->getPolicy([
            'policy' => $searchText,
        ]);
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify((string)ArrayHelper::getValue($model, 'policy'))->contains($searchText);
        }
    }

    private function getPolicy($searchParam)
    {
        $model = new PolicySearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);
        return $dataProvider->query->all();
    }
}