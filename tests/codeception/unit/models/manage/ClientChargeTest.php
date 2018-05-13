<?php

namespace models\manage;

use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;
use tests\codeception\unit\fixtures\ClientChargeFixture;
use tests\codeception\unit\fixtures\ClientChargePlanFixture;
use tests\codeception\unit\JmTestCase;

/**
 * Class ClientChargeTest
 * @package models\manage
 * @property ClientChargeFixture $client_charge
 * @property ClientChargePlanFixture $client_charge_plan
 */
class ClientChargeTest extends JmTestCase
{
    /**
     * beforSaveのtest
     */
    public function testBeforeSave()
    {
        $clientCharge = new ClientCharge();
        $clientCharge->limit_num = 99;

        $clientCharge->limitType = ClientCharge::LIMITED;
        $clientCharge->beforeSave(true);
        verify($clientCharge->limit_num)->equals(99);

        $clientCharge->limitType = ClientCharge::UNLIMITED;
        $clientCharge->beforeSave(true);
        verify($clientCharge->limit_num)->null();
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数値検証', function () {
            $charge = new ClientCharge();
            $charge->id = 'test';
            $charge->client_charge_plan_id = 'test';
            $charge->client_master_id = 'test';
            $charge->validate();
            verify($charge->hasErrors('id'))->true();
            verify($charge->hasErrors('client_charge_plan_id'))->true();
            verify($charge->hasErrors('client_master_id'))->true();
        });

        $this->specify('booleanチェック', function () {
            $charge = new ClientCharge();
            $charge->limitType = 3;
            $charge->validate();
            verify($charge->hasErrors('limitType'))->true();
        });

        $this->specify('枠上限検証', function () {
            $charge = new ClientCharge();
            // 上限あり入力なし⇒×
            $charge->limitType = ClientCharge::LIMITED;
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->true();
            // 上限あり文字列入力⇒×
            $charge->limit_num = '文字列';
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->true();
            // 上限あり数値オーバー⇒×
            $charge->limit_num = 256;
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->true();
            // 上限あり数値0以下⇒×
            $charge->limit_num = -1;
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->true();

            // 上限なし入力なし⇒○
            $charge->limitType = ClientCharge::UNLIMITED;
            $charge->limit_num = null;
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->false();
            // 上限なし文字列入力⇒○
            $charge->limit_num = '文字列';
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->false();
            // 上限なし数値オーバー⇒○
            $charge->limit_num = 256;
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->false();
            // 上限なし数値0以下⇒○
            $charge->limit_num = -1;
            $charge->validate();
            verify($charge->hasErrors('limit_num'))->false();
        });

        $this->specify('ひとつも選択されていない時', function () {
            $charge = new ClientCharge();
            $charge->noSelect = true;
            $charge->client_charge_plan_id = 1;
            $charge->client_master_id = 1;
            $charge->limitType = ClientCharge::LIMITED;
            $charge->limit_num = 255;
            $charge->validate();
            verify($charge->hasErrors('client_charge_plan_id'))->true();
            verify($charge->hasErrors('limitType'))->true();
            verify($charge->hasErrors('limit_num'))->true();
        });

        $this->specify('正しい値', function () {
            $charge = new ClientCharge();
            $charge->id = 1;
            $charge->client_charge_plan_id = 1;
            $charge->client_master_id = 1;
            $charge->limitType = ClientCharge::LIMITED;
            $charge->limit_num = 255;
            $charge->validate();
            verify($charge->validate())->true();
        });
    }

    /**
     * ラベルテスト
     */
    public function testAttributeLabels()
    {
        $charge = new ClientCharge();
        verify($charge->attributeLabels())->notEmpty();
    }

    /**
     * limitTypeのgetterとsetterのtest
     */
    public function testLimitType()
    {
        $this->specify('limit_numが空', function () {
            $model = new ClientCharge();
            verify($model->limitType)->equals(ClientCharge::UNLIMITED);
        });

        $this->specify('limit_numがある', function () {
            $model = new ClientCharge();
            $model->limit_num = 9;
            verify($model->limitType)->equals(ClientCharge::LIMITED);
        });

        $this->specify('limitTypeに値を代入', function () {
            $model = new ClientCharge();
            $model->limitType = ClientCharge::UNLIMITED;
            verify($model->limitType)->equals(ClientCharge::UNLIMITED);
        });
    }

    /**
     * (CSV入力規則に使用する)料金プランリストのtest
     */
    public function testGetClientChargePlanList()
    {
        $list = ClientCharge::getClientChargePlanList();
        foreach ($list AS $v){
            /* 料金プランのテスト */
            $planNo = $v['client_charge_plan_no'];
            $plan = ClientChargePlan::findOne(['client_charge_plan_no' => $planNo]);
            verify($plan->valid_chk)->equals(ClientChargePlan::VALID);  //有効のもののみか
            verify($plan->plan_name)->equals($v['plan_name']);  //料金プラン名が一致しているか
            verify($plan->period)->equals($v['period']);  //有効期限が一致しているか

            /* 掲載企業のテスト */
            //'client_name'として出力されるものが、[掲載企業名]([掲載企業No])にしているため、正規表現で取得している。
            preg_match('/(.+)\(([\d]+)\)$/', $v['client_name'], $results);
            $clientName = $results[1];  //掲載企業名を取得
            $clientNo = $results[2];    //掲載企業Noを取得
            //掲載企業Noからユニークなモデルを取得
            $client = ClientMaster::findOne(['client_no' => $clientNo]);
            verify($client->client_name)->equals($clientName);  //掲載企業名が一致しているか
        }
    }
}
