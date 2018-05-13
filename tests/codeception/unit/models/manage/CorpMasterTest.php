<?php
namespace models\manage;

use Yii;
use app\models\manage\CorpMaster;
use yii\helpers\ArrayHelper;
use tests\codeception\unit\JmTestCase;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;

/**
 * Class CorpMasterTest
 * @package models\manage
 * @property CorpMasterFixture $corp_master
 * @property ClientMasterFixture $client_master
 */
class CorpMasterTest extends JmTestCase
{
    public function testAttributeLabels()
    {
        $model = new CorpMaster();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * ルールテスト（審査機能ON）
     */
    public function testRules1()
    {
        // 審査機能をONにする
        Yii::$app->tenant->tenant->review_use = 1;

        $this->specify('必須チェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'corp_review_flg' => null,
                'valid_chk' => null,
                'corp_name' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('corp_review_flg'))->true();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('corp_name'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('id'))->true();
        });

        $this->specify('booleanチェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'corp_review_flg' => 3,
                'valid_chk' => 3,
            ]]);
            $model->validate();
            verify($model->hasErrors('corp_review_flg'))->true();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('ユニークチェック', function () {
            $model = new CorpMaster();
            $id = $this->id(2, 'corp_master');
            $corp = CorpMaster::findOne($id);
            $model->corp_name = $corp->corp_name;
            $model->validate();
            verify($model->hasErrors('corp_name'))->true();
        });
        $this->specify('代理店審査中求人の有無チェック', function () {
            // 代理店審査求人持ちの代理店
            $id = 7;

            $corp = CorpMaster::findOne($id);
            $corp->corp_review_flg = 0;
            $corp->validate();
            verify($corp->hasErrors('corp_review_flg'))->true();

            $corp->corp_review_flg = 1;
            $corp->validate();
            verify($corp->hasErrors('corp_review_flg'))->false();

            $corp = new CorpMaster();
            $corp->corp_review_flg = 0;
            $corp->validate();
            verify($corp->hasErrors('corp_review_flg'))->false();
        });
        $this->specify('正しいチェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'id' => 10000,
                'corp_review_flg' => 1,
                'valid_chk' => 1,
                'corp_name' => 'test',
            ]]);
            // functionItemSetの分があるので個別で確認
            $model->validate();
            verify($model->hasErrors('id'))->false();
            verify($model->hasErrors('corp_review_flg'))->false();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('corp_name'))->false();
        });
    }

    /**
     * ルールテスト（審査機能OFF）
     */
    public function testRules2()
    {
        // 審査機能をOFFにする
        Yii::$app->tenant->tenant->review_use = 0;

        $this->specify('必須チェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'corp_review_flg' => null,
                'valid_chk' => null,
                'corp_name' => null,
            ]]);
            $model->validate();
            verify($model->hasErrors('corp_review_flg'))->false();
            verify($model->hasErrors('valid_chk'))->true();
            verify($model->hasErrors('corp_name'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'id' => '文字列',
            ]]);
            $model->validate();
            verify($model->hasErrors('id'))->true();
        });

        $this->specify('booleanチェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'corp_review_flg' => 3,
                'valid_chk' => 3,
            ]]);
            $model->validate();
            verify($model->hasErrors('corp_review_flg'))->false();
            verify($model->hasErrors('valid_chk'))->true();
        });
        $this->specify('ユニークチェック', function () {
            $model = new CorpMaster();
            $id = $this->id(2, 'corp_master');
            $corp = CorpMaster::findOne($id);
            $model->corp_name = $corp->corp_name;
            $model->validate();
            verify($model->hasErrors('corp_name'))->true();
        });
        $this->specify('代理店審査中求人の有無チェック', function () {
            // 代理店審査求人持ちの代理店
            $id = 7;

            $corp = CorpMaster::findOne($id);
            $corp->corp_review_flg = 0;
            $corp->validate();
            verify($corp->hasErrors('corp_review_flg'))->false();

            $corp->corp_review_flg = 1;
            $corp->validate();
            verify($corp->hasErrors('corp_review_flg'))->false();

            $corp = new CorpMaster();
            $corp->corp_review_flg = 0;
            $corp->validate();
            verify($corp->hasErrors('corp_review_flg'))->false();
        });
        $this->specify('正しいチェック', function () {
            $model = new CorpMaster();
            $model->load([$model->formName() => [
                'id' => 10000,
                'corp_review_flg' => 1,
                'valid_chk' => 1,
                'corp_name' => 'test',
            ]]);
            // functionItemSetの分があるので個別で確認
            $model->validate();
            verify($model->hasErrors('id'))->false();
            verify($model->hasErrors('corp_review_flg'))->false();
            verify($model->hasErrors('valid_chk'))->false();
            verify($model->hasErrors('corp_name'))->false();
        });

        // 審査機能をONにする
        Yii::$app->tenant->tenant->review_use = 1;
    }

    /**
     * 登録前処理テスト
     */
    public function testBeforeSave()
    {
        $model = new CorpMaster();
        $model->beforeSave(true);
        verify($model->corp_no)->equals(CorpMasterFixture::RECORDS_PER_TENANT + 1);
    }

    /**
     * getFormatTableのtest
     */
    public function testGetFormatTable()
    {
        $model = new CorpMaster();
        $array = [
            'valid_chk' => [1 => Yii::t('app', '有効'), 0 => Yii::t('app', '無効')],
            'corp_review_flg' => [1 => Yii::t('app', 'あり'), 0 => Yii::t('app', 'なし')],
        ];
        verify($model->formatTable)->equals($array);
    }

    /**
     * getValidChkNameのtest
     */
    public function testGetValidChkName()
    {
        $model = new CorpMaster();
        $model->valid_chk = 0;
        verify($model->getValidChkName())->equals('無効');
        $model->valid_chk = 1;
        verify($model->getValidChkName())->equals('有効');
    }

    /**
     * dropDown用配列生成テスト
     */
    public function testGetDropDownArray()
    {
        $this->specify('検索条件なし', function () {
            $records = array_filter(self::getFixtureInstance('corp_master')->data(), function ($corp) {
                return $corp['tenant_id'] == Yii::$app->tenant->id;
            });
            $array = ['' => '初期文字列'] + ArrayHelper::map($records, 'id', 'corp_name');
            verify(CorpMaster::getDropDownArray('初期文字列'))->notEmpty();
            verify(CorpMaster::getDropDownArray('初期文字列'))->equals($array);
        });
        $this->specify('corpのvalid_chk検索', function () {
            $records = array_filter(self::getFixtureInstance('corp_master')->data(), function ($corp) {
                return $corp['tenant_id'] == Yii::$app->tenant->id && $corp['valid_chk'] == 1;
            });
            $array = ArrayHelper::map($records, 'id', 'corp_name');
            verify(CorpMaster::getDropDownArray(null, 1))->notEmpty();
            verify(CorpMaster::getDropDownArray(null, 1))->equals($array);
        });
        $this->specify('corpとclientのvalid_chk検索', function () {
            $records = array_filter(self::getFixtureInstance('corp_master')->data(), function ($corp) {
                $clients = $this->findRecordsByForeignKey(self::getFixtureInstance('client_master'), 'corp_master_id', $corp['id']);
                $validClients = array_filter($clients, function ($client) {
                    return $client['tenant_id'] == Yii::$app->tenant->id;
                });
                return $corp['tenant_id'] == Yii::$app->tenant->id && $corp['valid_chk'] == 1 && count($validClients) >= 1;
            });
            $array = ArrayHelper::map($records, 'id', 'corp_name');
            verify(CorpMaster::getDropDownArray(null, 1))->notEmpty();
            verify(CorpMaster::getDropDownArray(null, 1))->equals($array);
        });
    }
}