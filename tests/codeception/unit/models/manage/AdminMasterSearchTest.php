<?php

namespace models\manage;

use app\models\manage\AdminMaster;
use app\models\manage\AdminMasterSearch;
use app\models\manage\ClientMaster;
use tests\codeception\unit\fixtures\AdminMasterFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use Yii;
use yii\helpers\ArrayHelper;
use tests\codeception\unit\JmTestCase;
use app\modules\manage\models\Manager;
use yii\helpers\Json;

//todo tenant2ではテストが通らないので修正する
/**
 * @group admin
 */
class AdminMasterSearchTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数字チェック', function () {
            $model = new AdminMasterSearch();
            $model->load([$model->formName() => [
                'corp_master_id' => '文字列',
                'client_master_id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('corp_master_id'))->true();
            verify($model->hasErrors('client_master_id'))->true();
        });

        $this->specify('booleanチェック', function () {
            $model = new AdminMasterSearch();
            $model->load([$model->formName() => [
                'valid_chk' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new AdminMasterSearch();
            $model->load([$model->formName() => [
                'searchText' => 'あああ',
                'searchItem' => 'all',
                'valid_chk' => 1,
                'corp_master_id' => 1,
                'client_master_id' => 1,
                'role' => 'あああ',
            ]]);
            verify($model->validate())->true();
        });
    }

    /**
     * 検索テスト
     * 手間がかかりすぎるので一旦はキーワード検索のallは検証していません。
     * todo all検索テスト実装
     */
    public function testSearch()
    {
        $this->specify('初期状態で検索', function () {
            $models = $this->getAdminMaster([
                'searchText' => '',
                'searchItem' => 'all',
                'valid_chk' => '',
                'corp_master_id' => '',
                'client_master_id' => '',
                'role' => '',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'login_id'))->notEmpty();
            }
        });

        $this->specify('クリアボタン押下時', function () {
            $models = $this->getAdminMaster(1);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'login_id'))->notEmpty();
            }
        });

        $this->specify('キーワードで検索(フルネーム)', function () {
            $models = $this->getAdminMaster([
                'searchText' => 's',
                'searchItem' => 'fullName',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'fullName'))->contains('s');
            }
        });

        $this->specify('キーワードで検索(ログインID)', function () {
            $models = $this->getAdminMaster([
                'searchText' => 's',
                'searchItem' => 'login_id',
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'login_id'))->contains('s');
            }
        });

        $this->specify('代理店で検索', function () {
            $models = $this->getAdminMaster([
                'corp_master_id' => $this->id(1, 'corp_master'),
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'corp_master_id'))->equals($this->id(1, 'corp_master'));
            }
        });

        $this->specify('掲載企業で検索', function () {
            $models = $this->getAdminMaster([
                'client_master_id' => $this->id(1, 'client_master'),
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'client_master_id'))->equals($this->id(1, 'client_master'));
            }
        });

        $this->specify('運営管理者で検索', function () {
            $models = $this->getAdminMaster([
                'role' => Manager::OWNER_ADMIN,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'corp_master_id'))->equals(null);
                verify(ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
            }
        });

        $this->specify('代理店管理者権限で検索', function () {
            $models = $this->getAdminMaster([
                'role' => Manager::CORP_ADMIN,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'corp_master_id'))->notEmpty();
                verify(ArrayHelper::getValue($model, 'client_master_id'))->equals(null);
            }
        });

        $this->specify('掲載企業管理者権限で検索', function () {
            $models = $this->getAdminMaster([
                'role' => Manager::CLIENT_ADMIN,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'corp_master_id'))->notEmpty();
                verify(ArrayHelper::getValue($model, 'client_master_id'))->notEmpty();
            }
        });

        $this->specify('有効で検索', function () {
            $models = $this->getAdminMaster([
                'valid_chk' => 1,
            ]);
            verify($models)->notEmpty();
            foreach ($models as $model) {
                verify(ArrayHelper::getValue($model, 'valid_chk'))->equals(1);
            }
        });
    }

    private function getAdminMaster($searchParam)
    {
        $model = new AdminMasterSearch();
        $dataProvider = $model->search([$model->formName() => $searchParam]);

        return $dataProvider->query->all();
    }

    /**
     * CSVダウンロードテスト
     */
    public function testCsvSearch()
    {
        $this->setIdentity('owner_admin');
        // allCheck無し、id=1～5選択
        $id1 = $this->id(1, 'admin_master');
        $id2 = $this->id(2, 'admin_master');
        $id3 = $this->id(3, 'admin_master');
        $id4 = $this->id(4, 'admin_master');
        $id5 = $this->id(5, 'admin_master');
        $post = [
            'gridData' => Json::encode([
                'searchParams' => ['AdminMasterSearch' => 1],
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
            'AdminMasterSearch' => 1,
        ];
        // 被検証モデルと検証モデルの生成
        $searchModel = new AdminMasterSearch();
        $dataProvider = $searchModel->csvSearch($post);
        /** @var AdminMasterSearch[] $models */
        $models = ArrayHelper::index($dataProvider->models, 'id');
        /** @var AdminMasterSearch[] $expectedModels */
        $expectedModels = ArrayHelper::index(AdminMasterSearch::find()->where(['in', 'id', [$id1, $id2, $id3, $id4, $id5]])->all(), 'id');
        // 数と内容を検証
        verify($dataProvider->totalCount)->equals(5);
        verify($models[$id1]->attributes)->equals($expectedModels[$id1]->attributes);
        verify($models[$id2]->attributes)->equals($expectedModels[$id2]->attributes);
        verify($models[$id3]->attributes)->equals($expectedModels[$id3]->attributes);
        verify($models[$id4]->attributes)->equals($expectedModels[$id4]->attributes);
        verify($models[$id5]->attributes)->equals($expectedModels[$id5]->attributes);
    }

    /**
     * deleteSearchメソッドとbackupAndDeleteメソッドのテスト
     */
    public function testDelete()
    {
        // allCheck無し、id=1のみ選択
        $post = [
            'gridData' => Json::encode([
                'searchParams' => ['AdminMasterSearch' => $this->id(1, 'admin_master')],
                'totalCount' => AdminMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    $this->id(1, 'admin_master'),
                ],
                'allCheck' => false,
            ]),
            'AdminMasterSearch' => $this->id(1, 'admin_master'),
        ];
        $searchModel = new AdminMasterSearch();
        $expectedModel = AdminMasterSearch::findOne($this->id(1, 'admin_master'));
        // deleteSearchで取得したmodelの内容検証
        $deleteModels = $searchModel->deleteSearch($post);
        $deleteModel = $deleteModels[0];
        verify($deleteModel[0])->equals($expectedModel->id);

        // allCheckあり、1のみ未選択（削除件数のみ検証）
        $post = [
            'gridData' => Json::encode([
                'searchParams' => ['AdminMasterSearch' => $this->id(1, 'admin_master')],
                'totalCount' => AdminMasterFixture::RECORDS_PER_TENANT,
                'selected' => [
                    $this->id(1, 'admin_master'),
                    $this->id(2, 'admin_master'),
                ],
                'allCheck' => true,
            ]),
            'AdminMasterSearch' => $this->id(1, 'admin_master'),
        ];
        $deleteModels = $searchModel->deleteSearch($post);
        verify(count($deleteModels))->equals(AdminMasterFixture::RECORDS_PER_TENANT - 2);

        // 削除したものを元に戻す
        self::getFixtureInstance('admin_master')->load();
    }

    /**
     * listItem, value値の取得テスト
     */
    public static function testGetColumnName()
    {
        verify(AdminMasterSearch::getColumnName('corp_master_id'))->equals('corpMaster.corp_name');
        verify(AdminMasterSearch::getColumnName('client_master_id'))->equals('clientMaster.client_name');
        verify(AdminMasterSearch::getColumnName('fullName'))->equals('fullName');
        verify(AdminMasterSearch::getColumnName('name_sei'))->equals('name_sei');
    }

}
