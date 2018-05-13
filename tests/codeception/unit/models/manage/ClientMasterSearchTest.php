<?php

namespace models\manage;

use app\models\manage\ClientMaster;
use app\modules\manage\models\Manager;
use yii;
use tests\codeception\unit\JmTestCase;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use app\models\manage\ClientMasterSearch;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\ClientChargeFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use tests\codeception\unit\fixtures\ClientChargePlanFixture;

class ClientMasterSearchTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->setIdentity('owner_admin');

        $this->specify('数字チェック', function () {
            $model = new ClientMasterSearch();
            $model->load([$model->formName() => [
                'clientChargePlanId' => '文字列',
                'clientChargeType' => '文字列',
                'valid_chk' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('clientChargePlanId'))->true();
            verify($model->hasErrors('clientChargeType'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = new ClientMasterSearch();
            $model->load([$model->formName() => [
                'searchItem' => ['a'],
                'searchText' => ['a'],
            ]]);
            $model->validate();
            verify($model->hasErrors('searchItem'))->true();
            verify($model->hasErrors('searchText'))->true();
        });

        $this->specify('boolチェック', function () {
            $model = new ClientMasterSearch();
            $model->load([$model->formName() => [
                'valid_chk' => 3,
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('正しいパターン', function () {
            $model = new ClientMasterSearch();
            $model->load([$model->formName() => [
                'clientChargePlanId' => 1,
                'clientChargeType' => 1,
                'searchItem' => '文字列',
                'searchText' => '文字列',
                'valid_chk' => 1,
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索テスト
     */
    public function testSearchByOwner()
    {
        $this->setIdentity('owner_admin');

        /**
         *「すべてで検索」は現状、代理店名のみで検索している。
         *TODO:要テスト内容追加（代理店名のみでなく、他カラム内容での検索テストも追加）
         */
        $this->specify('すべてで検索', function () {
            $cmFixtureRecord = self::getFixtureInstance('corp_master')->data();
            $searchText = $cmFixtureRecord[0]['corp_name'];
            /** @var ClientMaster[] $models */
            $models = $this->getClientMaster([
                'searchItem' => 'all',
                'searchText' => $searchText,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'corpMaster.corp_name'))->contains($searchText);
            }
        });

        $this->specify('client_noで検索', function () {
            $cmFixtureRecord = self::getFixtureInstance('client_master')->data();
            $clientNo = $cmFixtureRecord[0]['client_no'];
            $models = $this->getClientMaster([
                'searchItem' => 'client_no',
                'searchText' => "$clientNo",
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'client_no'))->contains("$clientNo");
            }
        });

        $this->specify('addressで検索', function () {
            $searchText = '9';
            $models = $this->getClientMaster([
                'searchItem' => 'address',
                'searchText' => $searchText,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'address'))->contains($searchText);
            }
        });

        //TODO:そもそものテーブルの構造を含め、書き方を見なおしたほうがよい。
        $this->specify('掲載タイプで検索', function () {
            $models = $this->getClientMaster([
                'clientChargeType' => 1,
            ]);
            $ccpFixtureRecord = self::getFixtureInstance('client_charge_plan')->data();
            $ccpFixtureRecord = array_filter($ccpFixtureRecord, function($record){
                return $record['client_charge_type'] == 1;
            });
            $ccpFixtureRecord = array_unique(ArrayHelper::getColumn($ccpFixtureRecord, 'id'));
            $ccFixtureRecord = self::getFixtureInstance('client_charge')->data();
            $ccFixtureRecord = array_filter($ccFixtureRecord, function($record) use($ccpFixtureRecord){
                return in_array($record['client_charge_plan_id'], $ccpFixtureRecord);
            });
            $ccFixtureRecord = array_unique(ArrayHelper::getColumn($ccFixtureRecord, 'client_master_id'));
            foreach ($models as $model) {
                verify(in_array($model->id, $ccFixtureRecord))->true();
            }
        });

        $this->specify('課金タイプで検索', function () {
            $models = $this->getClientMaster([
                'clientChargePlanId' => 1,
            ]);
            $ccFixtureRecord = self::getFixtureInstance('client_charge')->data();
            $ccFixtureRecord = array_filter($ccFixtureRecord, function($record) {
                return $record['client_charge_plan_id'] == 1;
            });
            $ccFixtureRecord = array_unique(ArrayHelper::getColumn($ccFixtureRecord, 'client_master_id'));
            foreach ($models as $model) {
                verify(in_array($model->id, $ccFixtureRecord))->true();
            }
        });

        $this->specify('有効・無効で検索', function () {
            $models = $this->getClientMaster([
                'valid_chk' => 1,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify($model->valid_chk)->equals(1);
            }
        });
    }

    /**
     * 検索テスト：代理店管理者権限
     */
    public function testSearchByCorp()
    {
        $this->setIdentity('corp_admin');
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $corpMasterId = $identity->corp_master_id;

        $this->specify('すべてで検索', function () {
            $cmFixtureRecord = self::getFixtureInstance('corp_master')->data();
            $searchText = $cmFixtureRecord[0]['corp_name'];
            /** @var ClientMaster[] $models */
            $models = $this->getClientMaster([
                'searchItem' => 'all',
                'searchText' => $searchText,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'corpMaster.corp_name'))->contains($searchText);
            }
        });

        $this->specify('初期状態で検索', function () use($corpMasterId) {
            $models = $this->getClientMaster([]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify((string)ArrayHelper::getValue($model, 'corp_master_id'))->equals($corpMasterId);
            }
        });
    }

    /**
     * 検索テスト：掲載企業管理者権限
     * TODO:そもそもテストケースかくべきか
     */
    public function testSearchByClient()
    {
        $this->setIdentity('client_admin');
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        $clientMasterId = $identity->client_master_id;

        $this->specify('初期状態で検索', function () use($clientMasterId) {
            $models = $this->getClientMaster([]);
            verify($models)->isEmpty();
        });
    }

    /**
     * CSVダウンロードテスト
     */
    public function testCsvSearch()
    {
        $this->setIdentity('owner_admin');
        // allCheck無し、id=1～5選択
        $id1 = $this->id(1, 'client_master');
        $id2 = $this->id(2, 'client_master');
        $id3 = $this->id(3, 'client_master');
        $id4 = $this->id(4, 'client_master');
        $id5 = $this->id(5, 'client_master');

        $post = [
            'gridData' => Json::encode([
                'searchParams' => ['ClientMasterSearch' => 1],
                'totalCount' => 200,
                'selected' => [
                    Json::encode(['id' => $id1, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id2, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id3, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id4, 'tenant_id' => Yii::$app->tenant->id]),
                    Json::encode(['id' => $id5, 'tenant_id' => Yii::$app->tenant->id]),
                ],
                'allCheck' => false,
            ], 0),
            'ClientMasterSearch' => 1,
        ];
        // 被検証モデルと検証モデルの生成
        $searchModel = new ClientMasterSearch();
        $dataProvider = $searchModel->csvSearch($post);
        /** @var ClientMasterSearch[] $models */
        $models = ArrayHelper::index($dataProvider->models, 'id');
        /** @var ClientMasterSearch[] $expectedModels */
        $expectedModels = ArrayHelper::index(ClientMasterSearch::find()->where(['in', 'id', [$id1, $id2, $id3, $id4, $id5]])->all(), 'id');
        // 数と内容を検証
        verify($dataProvider->totalCount)->equals(5);
        verify($models[$id1]->attributes)->equals($expectedModels[$id1]->attributes);
        verify($models[$id2]->attributes)->equals($expectedModels[$id2]->attributes);
        verify($models[$id3]->attributes)->equals($expectedModels[$id3]->attributes);
        verify($models[$id4]->attributes)->equals($expectedModels[$id4]->attributes);
        verify($models[$id5]->attributes)->equals($expectedModels[$id5]->attributes);
    }

    /**
     * @param $searchParam
     * @return mixed
     */
    private function getClientMaster($searchParam)
    {
        $model = new ClientMasterSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * カラム名取得テスト。
     */
    public function testGetColumnName()
    {
        verify(ClientMasterSearch::getColumnName('client_id'))->notEmpty();
        verify(ClientMasterSearch::getColumnName('corpMaster'))->notEmpty();
    }
}
