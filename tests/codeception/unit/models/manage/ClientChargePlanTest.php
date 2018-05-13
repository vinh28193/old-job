<?php

namespace chargePlans\manage;

use app\models\manage\ClientMaster;
use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\ClientChargePlan;
use tests\codeception\unit\fixtures\ClientChargeFixture;
use tests\codeception\unit\fixtures\ClientChargePlanFixture;
use tests\codeception\fixtures\DispTypeFixture;
use yii\helpers\ArrayHelper;

/**
 * Class ClientChargePlanTest
 * @package chargePlans\manage
 *
 * @property ClientChargePlanFixture $client_charge_plan
 * @property ClientChargeFixture $client_charge
 * @property DispTypeFixture $disp_type
 */
class ClientChargePlanTest extends JmTestCase
{
    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('数値検証', function () {
            $chargePlan = new ClientChargePlan();
            $chargePlan->id = 'test';
            $chargePlan->client_charge_plan_no = 'test';
            $chargePlan->client_charge_type = 'test';
            $chargePlan->disp_type_id = 'test';
            $chargePlan->price = 'test';
            $chargePlan->valid_chk = 'test';
            $chargePlan->validate();
            verify($chargePlan->hasErrors('id'))->true();
            verify($chargePlan->hasErrors('client_charge_plan_no'))->true();
            verify($chargePlan->hasErrors('client_charge_type'))->true();
            verify($chargePlan->hasErrors('disp_type_id'))->true();
            verify($chargePlan->hasErrors('price'))->true();
            verify($chargePlan->hasErrors('valid_chk'))->true();
        });

        $this->specify('文字列検証', function () {
            $chargePlan = new ClientChargePlan();
            $chargePlan->plan_name = 1;
            $chargePlan->validate();
            verify($chargePlan->hasErrors('plan_name'))->true();
        });

        $this->specify('正しい値', function () {
            $chargePlan = new ClientChargePlan();
            $chargePlan->id = 1;
            $chargePlan->client_charge_plan_no = 1;
            $chargePlan->client_charge_type = 1;
            $chargePlan->disp_type_id = 1;
            $chargePlan->price = 1;
            $chargePlan->valid_chk = 1;
            $chargePlan->plan_name = 'test';
            $chargePlan->validate();
            verify($chargePlan->validate())->true();
        });

    }

    /**
     * ラベルテスト
     */
    public function testAttributeLabels()
    {
        $chargePlan = new ClientChargePlan();
        verify($chargePlan->attributeLabels())->notEmpty();
    }

    /**
     * 申し込みプラン取得テスト
     * todo valid検索条件のtest追加
     */
    public function testGetDropDownList()
    {
        $this->specify('検索条件なし(有効なものだけ)', function () {
            $chargePlanList = ClientChargePlan::getDropDownArray(false);
            foreach ($chargePlanList as $id => $planName) {
                $target = $this->findRecordById(self::getFixtureInstance('client_charge_plan'), $id);
                verify($target['valid_chk'])->equals(1);
            }
        });

        $this->specify('掲載企業検索条件', function () {
            $clientMasterId = 2;
            $defaultLabel = '全て';
            $dropDownLists = ClientChargePlan::getDropDownArray($defaultLabel, $clientMasterId);
            verify($dropDownLists)->notEmpty();
            $plans = ArrayHelper::getColumn(ClientMaster::findOne($clientMasterId)->clientCharges, 'clientChargePlan');
            $i = 1; // '全て'の分
            foreach ($plans as $plan) {
                /** @var ClientChargePlan $plan */
                if ($plan->valid_chk) {
                    verify($dropDownLists[$plan->id])->equals($plan->plan_name);
                    $i++;
                }
            }
            verify($dropDownLists)->count($i);
        });

        $this->specify('課金種別検索条件', function () {
            $chargeType = 2;
            $chargePlanList = ClientChargePlan::getDropDownArray(false, null, $chargeType);
            foreach ($chargePlanList as $id => $planName) {
                $target = $this->findRecordById(self::getFixtureInstance('client_charge_plan'), $id);
                verify($target['client_charge_type'])->equals($chargeType);
            }
        });

        $this->specify('valid_chk検索条件', function () {
            $tenantRecords = array_filter(self::getFixtureInstance('client_charge_plan')->data(), function ($record) {
                return $record['tenant_id'] == 1;
            });

            $clientChargePlanVerify = ArrayHelper::map($tenantRecords, 'id', 'plan_name');
            verify(ClientChargePlan::getDropDownArray(false, null, null, null))->equals($clientChargePlanVerify);
        });
    }

    /**
     * indexedPlansのtest
     */
    public function testIndexedPlans()
    {
        $indexedPlans = ClientChargePlan::indexedPlans();
        foreach ($indexedPlans as $chargeType => $plans) {
            foreach ($plans as $plan) {
                /** @var ClientChargePlan $plan */
                verify($plan->client_charge_type)->equals($chargeType);
                verify($plan->valid_chk)->equals(1);
            }
        }
    }

    /**
     * getChargeTypeNameのtest
     */
    public function testGetChargeTypeName()
    {
        verify(ClientChargePlan::getChargeTypeName(ClientChargePlan::CHARGE_TYPE_DISPLAY))->equals('掲載課金');
        verify(ClientChargePlan::getChargeTypeName(ClientChargePlan::CHARGE_TYPE_EMPLOY))->equals('採用課金');
        verify(ClientChargePlan::getChargeTypeName(ClientChargePlan::CHARGE_TYPE_APPLY))->equals('応募課金');
    }

    /**
     * getChargeTypeArrayのtest
     */
    public function testGetChargeTypeArray()
    {
        verify(ClientChargePlan::getChargeTypeArray()[ClientChargePlan::CHARGE_TYPE_DISPLAY])->equals('掲載課金');
        verify(ClientChargePlan::getChargeTypeArray()[ClientChargePlan::CHARGE_TYPE_EMPLOY])->equals('採用課金');
        verify(ClientChargePlan::getChargeTypeArray()[ClientChargePlan::CHARGE_TYPE_APPLY])->equals('応募課金');
    }

    /**
     * getPlanPeriodArrayのtest
     */
    public function testGetPlanPeriodArray()
    {
        $plans = array_filter(self::getFixtureInstance('client_charge_plan')->data(), function ($record) {
            return $record['valid_chk'] == 1 && $record['tenant_id'] == 1;
        });
        $array = ArrayHelper::map($plans, 'id', 'period');
        verify(ClientChargePlan::getPlanPeriodArray())->equals($array);
    }
}
