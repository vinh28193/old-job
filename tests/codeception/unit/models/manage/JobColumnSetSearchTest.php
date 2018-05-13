<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\JobColumnSetSearch;
use tests\codeception\fixtures\JobColumnSetFixture;
use yii\helpers\ArrayHelper;

/**
 * Class JobColumnSetSearchTest
 * @package models\manage
 *
 * @property JobColumnSetFixture $job_column_set
 */
class JobColumnSetSearchTest extends JmTestCase
{
    /**
     * 検索test
     */
    public function testSearch()
    {
        // tenantのfixtureレコード取得
        $tenantRecords = array_filter(self::getFixtureInstance('job_column_set')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        $tenantRecords = array_values($tenantRecords);
        // 検索文字列取得
        $label = ArrayHelper::getValue($tenantRecords[2], 'label');
        $searchText = mb_substr($label, 3, 4);
        // 検証
        $models = $this->getJobColumnSet([
            'searchText' => $searchText,]
        );
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify((string)ArrayHelper::getValue($model, 'label'))->contains($searchText);
        }
    }

    private function getJobColumnSet($searchParam)
    {
        $model = new JobColumnSetSearch();
        $dataProvider = $model->search(['JobColumnSetSearch' => $searchParam]);
        return $dataProvider->query->all();
    }

}