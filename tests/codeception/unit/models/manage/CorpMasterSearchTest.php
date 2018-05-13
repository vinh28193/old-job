<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use yii;
use yii\helpers\ArrayHelper;
use app\models\manage\CorpMaster;
use app\models\manage\CorpMasterSearch;
use tests\codeception\unit\fixtures\CorpMasterFixture;

/**
 * Class CorpMasterSearchTest
 * @package models\manage
 *
 * @property CorpMasterFixture $corp_master
 */
class CorpMasterSearchTest extends JmTestCase
{
    /**
     * rules test
     */
    public function testRules()
    {
        $this->specify('文字列チェック', function () {
            $model = new CorpMasterSearch();
            $model->load([$model->formName() => [
                'searchItem' => (int)1,
                'searchText' => (int)1,
            ]]);
            $model->validate();
            verify($model->hasErrors('searchItem'))->true();
            verify($model->hasErrors('searchText'))->true();
        });
        $this->specify('booleanチェック', function () {
            $model = new CorpMasterSearch();
            $model->load([$model->formName() => [
                'corp_review_flg' => 3,
                'valid_chk' => 3,
            ]]);
            $model->validate();
            verify($model->hasErrors('corp_review_flg'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('正しいチェック', function () {
            $model = new CorpMasterSearch();
            $model->load([$model->formName() => [
                'searchItem' => "test",
                'searchText' => "test",
                'valid_chk' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索test
     */
    public function testSearch()
    {
        $search = new CorpMasterSearch();
        $corpFixtures = self::getFixtureInstance('corp_master')->data();
        $corpMasterVerify = array_filter($corpFixtures, function ($corpMaster) {
            return $corpMaster['tenant_id'] === Yii::$app->tenant->id;
        });
        $corpMasterVerify = array_values($corpMasterVerify);
        $params = [
            'CorpMasterSearch' => ['searchItem' => 'all'],
        ];
        $dataProvider = $search->search($params);
        verify(ArrayHelper::toArray($dataProvider->query->all()))->equals($corpMasterVerify);
    }

    /**
     * CSVダウンロードテスト
     */
    public function testCsvSearch()
    {
        $allRecords = self::getFixtureInstance('corp_master')->data();
        $id1 = $this->id(1, 'corp_master');
        $id2 = $this->id(2, 'corp_master');
        $id3 = $this->id(3, 'corp_master');

        //-------------------------------
        // オールチェックなし・個別チェックあり
        //-------------------------------
        $search = new CorpMasterSearch();
        $params = [
            'CorpMasterSearch' => [
                'searchItem' => 'all',
            ],
            'gridData' => json_encode([
                'searchParams' => [$search->formName() => ['searchItem' => 'all']],
                'totalCount' => CorpMasterFixture::RECORDS_PER_TENANT,
                'allCheck' => false,
                'selected' => [strval($id1), strval($id3)],
            ]),
        ];
        $dataProvider = $search->csvSearch($params);
        $targetRecords = array_filter($allRecords, function ($record) use ($id1, $id3) {
            return ArrayHelper::isIn($record['id'], [$id1, $id3]);
        });
        // 両方idでindex
        $expectedRecords = ArrayHelper::index($targetRecords, 'id');
        /** @var CorpMasterSearch[] $models */
        $models = ArrayHelper::index($dataProvider->models, 'id');
        // 検証
        verify($dataProvider->count)->equals(2);
        verify($models[$id1]->attributes)->equals($expectedRecords[$id1]);
        verify($models[$id3]->attributes)->equals($expectedRecords[$id3]);

        //-------------------------------
        // オールチェックあり・個別チェックあり
        //-------------------------------
        $search = new CorpMasterSearch();
        $params = [
            $search->formName() => [
                'searchItem' => 'all',
            ],
            'gridData' => json_encode([
                'searchParams' => [$search->formName() => ['searchItem' => 'all']],
                'totalCount' => CorpMasterFixture::RECORDS_PER_TENANT,
                'allCheck' => true,
                'selected' => [strval($id2)],
            ]),
        ];
        $dataProvider = $search->csvSearch($params);
        $targetRecords = array_filter($allRecords, function ($record) use ($id2) {
            return $record['id'] != $id2 && $record['tenant_id'] == Yii::$app->tenant->id;
        });
        // 両方idでindex
        $expectedRecords = ArrayHelper::index($targetRecords, 'id');
        /** @var CorpMasterSearch[] $models */
        $models = ArrayHelper::index($dataProvider->models, 'id');
        // 検証
        verify($models)->notEmpty();
        verify($models)->count(count($expectedRecords));
        foreach ($models as $id => $model) {
            verify($model->attributes)->equals($expectedRecords[$id]);
        }
    }
}