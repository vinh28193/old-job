<?php

namespace models\manage;

use yii;
use Codeception\Specify;
use tests\codeception\unit\JmTestCase;
use app\models\manage\ClientMaster;
use app\models\manage\ClientCharge;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use tests\codeception\unit\fixtures\ClientChargeFixture;
use tests\codeception\unit\fixtures\CorpMasterFixture;
use tests\codeception\fixtures\ClientColumnSetFixture;

class ClientMasterTest extends JmTestCase
{
    /**
     * test getter
     */
    public function testGetter()
    {
        $this->specify('getterのテスト', function () {
            $model = ClientMaster::findOne($this->id(1, 'client_master'));
            if($model){
                $name = 'clientCharge1';
                verify($model->$name)->isInstanceOf(ClientCharge::className());
            }
        });
    }

    /**
     * beforeSaveのテスト
     */
    public function testBeforeSave()
    {
        $clientMaster = new ClientMaster();
        $clientMaster2 = ClientMaster::findOne(1);
        $clientMaster->load($clientMaster2->attributes);

        $this->specify('掲載企業番号と掲載終了日の自動挿入テスト', function() use ($clientMaster){
            verify($clientMaster->beforeSave(true))->true();
            verify($clientMaster->client_no)->notEmpty();
            verify($clientMaster->created_at)->notEmpty();
        });
    }

    /**
     * beforeValidationのテスト（運営元権限）
     * （corp_master_id関係なくvalidate()がtrueになる
     * ことを担保している）
     */
    public function testBeforeValidateByOwner()
    {
        $this->specify('代理店IDがログインしている管理者のそれではないとき（運営元権限管理者）', function() {
            $this->setIdentity('owner_admin');
            $param = [
                'tenant_id' => '1',
                'corp_master_id' => '9999',
                'client_name' => '文字列',
                'client_name_kana' => '文字列',
                'tel_no' => '00000000000',
                'address' => '文字列',
                'tanto_name' => '文字列',
                'valid_chk' => '1',
                'clientChargePlan' => '1',
                'option106' => '文字列',
            ];
            $model = new ClientMaster();
            $model->load(['ClientMaster' => $param]);
            verify($model->validate())->true();
            verify($model->corp_master_id)->equals(9999);
        });
    }

    /**
     * beforeValidationのテスト（代理店権限）
     * （corp_master_id'関係なくvalidate()がログイン
     * 管理者のcorp_master_idになる
     */
    public function testBeforeValidateByCorp()
    {
        $this->specify('代理店IDがログインしている管理者のそれではないとき（代理店権限管理者）', function() {
            $this->setIdentity('corp_admin');
            $tenantId = Yii::$app->tenant->id;
            $param = [
                'tenant_id' => '1',
                'corp_master_id' => '9999',
                'client_name' => '文字列',
                'client_name_kana' => '文字列',
                'tel_no' => '00000000000',
                'address' => '文字列',
                'tanto_name' => '文字列',
                'valid_chk' => '1',
                'clientChargePlan' => '1',
                'option106' => '文字列',
            ];
            $model = new ClientMaster();
            $model->load(['ClientMaster' => $param]);
            verify($model->validate())->true();
            verify($model->corp_master_id)->equals(self::getFixtureInstance('admin_master')->data()["corp_admin$tenantId"]['corp_master_id']);
        });

        $this->specify('代理店IDがないとき（代理店権限管理者）', function() {
            $this->setIdentity('corp_admin');
            $tenantId = Yii::$app->tenant->id;
            $param = [
                'tenant_id' => '1',
                'corp_master_id' => '',
                'client_name' => '文字列',
                'client_name_kana' => '文字列',
                'tel_no' => '00000000000',
                'address' => '文字列',
                'tanto_name' => '文字列',
                'valid_chk' => '1',
                'clientChargePlan' => '1',
                'option106' => '文字列',
            ];
            $model = new ClientMaster();
            $model->load(['ClientMaster' => $param]);
            verify($model->validate())->true();
            verify($model->corp_master_id)->equals(self::getFixtureInstance('admin_master')->data()["corp_admin$tenantId"]['corp_master_id']);
        });
    }

    /**
     * beforeValidationのテスト（掲載管理者権限）
     * validate()がログインしている管理者に関わらず、
     * falseを返す。
     */
    public function testBeforeValidateByClient()
    {
        $this->specify('掲載企業管理者では県債企業管理者権限を', function() {
            $this->setIdentity('client_admin');
            $param = [
                'tenant_id' => '1',
                'corp_master_id' => '1',
                'client_name' => '文字列',
                'client_name_kana' => '文字列',
                'tel_no' => '00000000000',
                'address' => '文字列',
                'tanto_name' => '文字列',
                'valid_chk' => '1',
                'clientChargePlan' => '1',
                'option106' => '文字列',
            ];
            $model = new ClientMaster();
            $model->load(['ClientMaster' => $param]);
            verify(!$model->validate())->true();
        });
    }

    /**
     * rules() のテスト
     */
    public function testRules()
    {
        $param = [
            'corp_master_id' => '1',
            'client_name' => 'strings',
            'client_name_kana' => 'strings',
            'tel_no' => '00000000000',
            'tanto_name' => 'strings',
            'valid_chk' => '1',
            'admin_memo' => 'strings',
            'option106' => 'strings',
        ];

        $this->specify('正常な値', function() use($param) {
            $this->setIdentity('owner_admin');
            $model = new ClientMaster();
            $model->load(['ClientMaster' => $param]);
            verify($model->validate())->true();
        });

        $this->specify('corp_master_idの必須チェック', function() use($param) {
            $model = new ClientMaster();
            $param['corp_master_id'] = '';
            $model->load(['ClientMaster' => $param]);
            $model->validate();
            verify($model->hasErrors('corp_master_id'))->true();
        });

        $this->specify('client_nameの必須チェック', function() use($param) {
            $model = new ClientMaster();
            $param['client_name'] = '';
            $model->load(['ClientMaster' => $param]);
            $model->validate();
            verify($model->hasErrors('client_name'))->true();
        });

        $this->specify('valid_chkの必須チェック', function() use($param) {
            $model = new ClientMaster();
            $param['valid_chk'] = '';
            $model->load(['ClientMaster' => $param]);
            $model->validate();
            verify($model->hasErrors('valid_chk'))->true();
        });

        $this->specify('client_nameの重複チェック', function() use($param) {
            $model = new ClientMaster();
            $param['client_name'] = self::getFixtureInstance('client_master')[0]['client_name'];
            $model->load(['ClientMaster' => $param]);
            $model->validate();
            verify($model->hasErrors('client_name'))->true();
        });

        $this->specify('clientChargePlanの必須チェック', function() use($param) {
            $model = new ClientMaster();
            $param['clientChargePlan'] = false;
            $model->load(['ClientMaster' => $param]);
            $model->validate();
            verify($model->hasErrors('clientChargePlan'))->true();
        });
    }

    /**
     * リレーションのあるclientChargeを取得できているか
     */
    public function testGetClientChargeModels()
    {
        $param = [
            'corp_master_id' => '1',
            'client_name' => 'strings',
            'client_name_kana' => 'strings',
            'tel_no' => '00000000000',
            'tanto_name' => 'strings',
            'valid_chk' => '1',
            'admin_memo' => 'strings',
            'option106' => 'strings',
        ];

        $this->specify('対応するClientChargeモデルが取れているか', function() use($param) {
            $this->setIdentity('owner_admin');
            $model = new ClientMaster();
            $model->load(['ClientMaster' => $param]);
            if($model->clientCharges){
                foreach ($model->clientChargeModels AS $key => $val){
                    verify($val)->isInstanceOf(ClientCharge::className());
                    verify($val->client_charge_plan_id)->equals($key);
                }
            }
        });
    }

    /**
     * リレーションのあるclientChargeから、client_charge_plan_idを指定して取得できているかテスト
     */
    public function testGetClientChargeModel()
    {
        $param = [
            'corp_master_id' => '1',
            'client_name' => 'strings',
            'client_name_kana' => 'strings',
            'tel_no' => '00000000000',
            'tanto_name' => 'strings',
            'valid_chk' => '1',
            'admin_memo' => 'strings',
            'option106' => 'strings',
        ];

        $this->specify('対応するClientChargeモデルの中からclient_charge_plan_idが取れているか', function() use($param) {
            $this->setIdentity('owner_admin');
            $model = new ClientMaster();
            $model->load(['ClientMaster' => $param]);
            if($model->clientCharges){
                verify($model->getClientChargeModel(1)->client_charge_plan_id)->true(1);
            }
        });
    }
    
    /**
     * ドロップダウンリストのテスト
     */
    public function testGetDropDownArray()
    {
        // nullのパターンは時間がないため省略
        $this->specify('valid_chk、corp_masterで適切に絞られているかテスト', function() {
            $queryRecords = ClientMaster::getDropDownArray('', 0, 1);
            $fixtureRecords = self::getFixtureInstance('client_master')->data();
            $trueFixtureRecords = array_filter($fixtureRecords, function($record){
               return $record['valid_chk'] == 0 && $record['corp_master_id'] == 1;
            });
            $falseFixtureRecords = array_filter($fixtureRecords, function($record){
                return !($record['valid_chk'] == 0 && $record['corp_master_id'] == 1);
            });
            foreach ($trueFixtureRecords AS $fRecord){
                verify(in_array($fRecord['id'], array_keys($queryRecords)))->true();
            }
            foreach ($falseFixtureRecords AS $fRecord){
                verify(!in_array($fRecord['id'], array_keys($queryRecords)))->true();
            }
        });
    }
}