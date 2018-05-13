<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\AdminColumnSetSearch;
use tests\codeception\fixtures\AdminColumnSetFixture;
use yii\helpers\ArrayHelper;

/**
 * Class AdminColumnSetSearchTest
 * @package models\manage
 *
 * @property AdminColumnSetFixture $admin_column_set
 */
class AdminColumnSetSearchTest extends JmTestCase
{
    /**
     * 検索test
     */
    public function testSearch()
    {
        // tenantのfixtureレコード取得
        $tenantRecords = array_filter(self::getFixtureInstance('admin_column_set')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        $tenantRecords = array_values($tenantRecords);
        // 検索文字列取得
        $label = ArrayHelper::getValue($tenantRecords[2], 'label');
        $searchKey = mb_substr($label, 3, 4);
        // 検証
        $models = $this->getAdminColumnSet([
            'searchText' => $searchKey,
        ]);
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify((string)ArrayHelper::getValue($model, 'label'))->contains($searchKey);
        }
    }

    private function getAdminColumnSet($searchParam)
    {
        $model = new AdminColumnSetSearch();

        $dataProvider = $model->search(['AdminColumnSetSearch' => $searchParam]);
        return $dataProvider->query->all();
    }

}