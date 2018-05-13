<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\manage\ClientColumnSetSearch;
use tests\codeception\fixtures\ClientColumnSetFixture;

/**
 * Class ClientColumnSetSearchTest
 * @package models\manage
 *
 * @property ClientColumnSetFixture $client_column_set
 */
class ClientColumnSetSearchTest extends JmTestCase
{
    /**
     * 検索test
     */
    public function testSearch()
    {
        // tenantのfixtureレコード取得
        $tenantRecords = array_filter(self::getFixtureInstance('client_column_set')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        $tenantRecords = array_values($tenantRecords);
        // 検索文字列取得
        $label = ArrayHelper::getValue($tenantRecords[2], 'label');
        $searchKey = mb_substr($label, 3, 4);
        // 検証
        $models = $this->getClientColumnSet([
            'searchText' => $searchKey,
        ]);
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify((string)ArrayHelper::getValue($model, 'label'))->contains($searchKey);
        }
    }

    private function getClientColumnSet($searchParam)
    {
        $model = new ClientColumnSetSearch();
        $dataProvider = $model->search(['ClientColumnSetSearch' => $searchParam]);

        return $dataProvider->query->all();
    }
}