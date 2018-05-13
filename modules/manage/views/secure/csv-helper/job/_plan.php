<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/10/01
 * Time: 15:38
 */
use proseeds\helpers\CommonHtml;
use app\models\manage\ClientCharge;
use app\models\manage\ClientChargePlan;
use app\models\manage\ClientMaster;

$clientCharge = new ClientCharge();//やむを得ず
$clientChargePlan = new ClientChargePlan();//やむを得ず
$clientMaster = new ClientMaster();//やむを得ず

$header = [
    $clientChargePlan->getAttributeLabel('client_charge_plan_no'),
    $clientMaster->getAttributeLabel('client_name') . Yii::t('app', '(ID)'),
    $clientChargePlan->getAttributeLabel('plan_name'),
    $clientChargePlan->getAttributeLabel('period'),
];
$body = ClientCharge::getClientChargePlanList();
echo CommonHtml::tableView($header, $body);