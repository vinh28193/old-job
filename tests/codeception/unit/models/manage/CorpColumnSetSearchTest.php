<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\CorpColumnSetSearch;
use tests\codeception\fixtures\CorpColumnSetFixture;
use yii\helpers\ArrayHelper;

/**
 * Class CorpColumnSetSearchTest
 * @package models\manage
 *
 * @property CorpColumnSetFixture $corp_column_set
 */
class CorpColumnSetSearchTest extends JmTestCase
{
    /**
     * 検索test
     */
    public function testSearch()
    {
        // tenantのfixtureレコード取得
        $tenantRecords = array_filter(self::getFixtureInstance('corp_column_set')->data(), function ($record) {
            return $record['tenant_id'] == Yii::$app->tenant->id;
        });
        $tenantRecords = array_values($tenantRecords);
        // 検索文字列取得
        $label = ArrayHelper::getValue($tenantRecords[2], 'label');
        $searchKey = mb_substr($label, 3, 4);
        // 検証
        $models = $this->getCorpColumnSet([
            'searchText' => $searchKey,
        ]);
        verify($models)->notEmpty();
        foreach ($models as $model) {
            verify((string)ArrayHelper::getValue($model, 'label'))->contains($searchKey);
        }
    }

    private function getCorpColumnSet($searchParam)
    {
        $model = new CorpColumnSetSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);
        return $dataProvider->query->all();
    }

}