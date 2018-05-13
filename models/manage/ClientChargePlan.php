<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use proseeds\models\BaseModel;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "client_charge_plan".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $client_charge_plan_no
 * @property integer $client_charge_type
 * @property integer $disp_type_id
 * @property string $plan_name
 * @property integer $price
 * @property integer $valid_chk
 * @property integer $period
 *
 * @property dispType $dispType
 */
class ClientChargePlan extends BaseModel
{
    /** 有効or無効 */
    const VALID = 1;
    const INVALID = 0;
    /** 課金タイプ 掲載課金、採用課金、応募課金 */
    const CHARGE_TYPE_DISPLAY = 1;
    const CHARGE_TYPE_EMPLOY = 2;
    const CHARGE_TYPE_APPLY = 3;

    /**
     * テーブル名設定
     * @return string
     */
    public static function tableName()
    {
        return 'client_charge_plan';
    }

    /**
     * ルール設定
     * @return array
     */
    public function rules()
    {
        return [
            //[['client_charge_plan_no', 'price'], 'required'],
            [['id', 'client_charge_plan_no', 'client_charge_type', 'disp_type_id', 'price', 'valid_chk'], 'integer'],
            [['plan_name'], 'string']
        ];
    }

    /**
     * 要素の名前設定
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'client_charge_plan_no' => Yii::t('app', '{planLabel}No.', ['planLabel' => Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label]),
            'client_charge_type' => Yii::t('app', '課金タイプ'),
            'disp_type_id' => Yii::t('app', '掲載タイプ'),
            'price' => Yii::t('app', '料金'),
            'valid_chk' => Yii::t('app', '公開状況'),
            'plan_name' => Yii::t('app', '申込みプラン名'),
            'period' => Yii::t('app', '有効日数'),
        ];
    }

    /**
     * @param $defaultLabel
     * @param null $clientMasterId
     * @param null $chargeType
     * @param int $validChk
     * @return array
     */
    public static function getDropDownArray($defaultLabel, $clientMasterId = null, $chargeType = null, $validChk = self::VALID)
    {
        $query = self::find()->select([self::tableName() . '.id', self::tableName() . '.plan_name'])
            ->filterWhere([self::tableName() . '.valid_chk' => $validChk])
            ->andFilterWhere([self::tableName() . '.client_charge_type' => $chargeType]);
        if (!JmUtils::isEmpty($clientMasterId)) {
            $query->joinWith('clientCharge')->andWhere([ClientCharge::tableName() . '.client_master_id' => $clientMasterId]);
        }
        $array = ArrayHelper::map($query->all(), 'id', 'plan_name');
        return $defaultLabel ? ['' => $defaultLabel] + $array : $array;
    }

    /**
     * ClientChargeとのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getClientCharge()
    {
        return $this->hasMany(ClientCharge::className(), ['client_charge_plan_id' => 'id']);
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDispType()
    {
        return $this->hasOne(DispType::className(), ['id' => 'disp_type_id']);
    }

    /**
     * @return array
     */
    public static function indexedPlans()
    {
        return ArrayHelper::index(self::findAll(['valid_chk' => self::VALID]), null, 'client_charge_type');
    }

    /**
     * @param $chargeTypeNo
     * @return mixed
     */
    public static function getChargeTypeName($chargeTypeNo)
    {
        return ArrayHelper::getValue(self::getChargeTypeArray(), $chargeTypeNo);
    }

    /**
     * 課金タイプ一覧の取得
     * @return array 課金タイプ一覧
     */
    public static function getChargeTypeArray()
    {
        return [
            self::CHARGE_TYPE_DISPLAY => Yii::t('app', '掲載課金'),
            self::CHARGE_TYPE_EMPLOY => Yii::t('app', '採用課金'),
            self::CHARGE_TYPE_APPLY => Yii::t('app', '応募課金'),
        ];
    }

    /**
     * 配列取得
     * @return array
     */
    public static function getPlanPeriodArray()
    {
        return ArrayHelper::map(self::find()->select(['id', 'period'])->where(['valid_chk' => self::VALID])->all(), 'id', 'period');
    }
}
