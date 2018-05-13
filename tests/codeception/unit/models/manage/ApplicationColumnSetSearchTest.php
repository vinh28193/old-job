<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\ApplicationColumnSetSearch;
use tests\codeception\fixtures\ApplicationColumnSetFixture;
use yii\helpers\ArrayHelper;

/**
 * Class ApplicationColumnSetSearchTest
 * @package models\manage
 *
 * @property ApplicationColumnSetFixture $application_column_set
 */
class ApplicationColumnSetSearchTest extends JmTestCase
{
    /**
     * 検索test
     */
    public function testSearch()
    {
        // tenantのfixtureレコード取得
        $tenantRecords = array_filter(self::getFixtureInstance('application_column_set')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        $tenantRecords = array_values($tenantRecords);
        // 検索文字列取得
        $label = ArrayHelper::getValue($tenantRecords[2], 'label');
        $searchKey = mb_substr($label, 0, 2);
        // 検証
        $models = $this->getApplicationColumnSet([
            'searchText' => $searchKey,
        ]);
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify((string)ArrayHelper::getValue($model, 'label'))->contains($searchKey);
        }
    }

    private function getApplicationColumnSet($searchParam)
    {
        $model = new ApplicationColumnSetSearch();
        $dataProvider = $model->search(['ApplicationColumnSetSearch' => $searchParam]);

        return $dataProvider->query->all();
    }

}