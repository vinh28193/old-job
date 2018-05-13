<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;

use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "client_charge".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $client_charge_plan_id
 * @property integer $client_master_id
 * @property integer $limit_num
 * @property string $disp_end_date
 *
 * @property boolean $limitType
 *
 * @property ClientMaster $clientMaster
 * @property ClientChargePlan $clientChargePlan
 */
class ClientCharge extends BaseModel
{
    /** @var int 状態 - 有効 */
    const FLAG_VALID = 1;

    /** @var int 状態 - 無効 */
    const FLAG_UNVALID = 0;
    
    /** 上限ありorなし */
    const UNLIMITED = 0;
    const LIMITED = 1;
    /** @var int 上限ありorなし */
    private $_limitType;
    public $noSelect;

    /**
     * テーブル名設定
     * @return string
     */
    public static function tableName()
    {
        return 'client_charge';
    }

    /**
     * 保存前処理
     * 申し込みIDをユニークに保つためテナントごとの最大値＋1を挿入。
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //上限なしの場合、枠は強制的にnullにする。
            if ($this->limitType == self::UNLIMITED) {
                $this->limit_num = null;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * ルール設定
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'client_charge_plan_id', 'client_master_id'], 'integer'],
            ['limitType', 'boolean'],
            ['client_charge_plan_id', function ($attribute, $params) {
                if ($this->noSelect) {
                    $this->addError($attribute);
                    $this->addError('limitType');
                    $this->addError('limit_num');
                }
            }, 'skipOnEmpty' => false],
            ['limit_num', function ($attribute, $params) {
                if ($this->limitType == self::LIMITED) {
                    $requiredValidator = new RequiredValidator();
                    $requiredValidator->validateAttribute($this, $attribute);
                    $numberValidator = new NumberValidator(['max' => 255, 'min' => 1]);
                    $numberValidator->validateAttribute($this, $attribute);
                }
            }, 'skipOnEmpty' => false],
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
            'client_charge_plan_id' => Yii::t('app', '申込みプランID'),
            'client_master_id' => Yii::t('app', '掲載企業ID'),
            'limit_num' => Yii::t('app', '枠数'),
            'disp_end_date' => Yii::t('app', '掲載終了日'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientMaster()
    {
        return $this->hasOne(ClientMaster::className(), ['id' => 'client_master_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientChargePlan()
    {
        return $this->hasOne(ClientChargePlan::className(), ['id' => 'client_charge_plan_id', 'tenant_id' => 'tenant_id']);
    }

    /**
     * @return int
     */
    public function getLimitType()
    {
        if (!isset($this->_limitType)) {
            $this->_limitType = $this->limit_num ? self::LIMITED : self::UNLIMITED;
        }
        return $this->_limitType;
    }

    /**
     * @param $v
     */
    public function setLimitType($v)
    {
        $this->_limitType = $v;
    }

    /**
     * 求人原稿CSV一括登録する際の料金プラン・掲載企業Noの表にするための配列を返す
     * @return mixed
     */
    public static function getClientChargePlanList()
    {
        $charges = self::find()
            ->innerJoinWith(['clientChargePlan'])
            ->innerJoinWith(['clientMaster.corpMaster'])
            ->where([
                ClientChargePlan::tableName() . '.valid_chk' => ClientChargePlan::VALID,
                ClientMaster::tableName() . '.valid_chk' => ClientMaster::VALID,
                CorpMaster::tableName() . '.valid_chk' => CorpMaster::VALID,
            ])
            ->all();

        return array_filter(array_map(
            function (self $charge) {
                if ($charge->clientChargePlan && $charge->clientMaster) {
                    return [
                        'client_charge_plan_no' => $charge->clientChargePlan->client_charge_plan_no,
                        'client_name' => $charge->clientMaster->client_name . '(' . $charge->clientMaster->client_no . ')',
                        'plan_name' => $charge->clientChargePlan->plan_name,
                        'period' => $charge->clientChargePlan->period,
                    ];
                } else {
                    return [];
                }
            }, $charges));
    }

    /**
     * CSVダウンロード用ソースを生成
     * @return ActiveDataProvider
     */
    public function keyCsvSearch()
    {
        $query = self::find()
            ->innerJoinWith(['clientChargePlan'])
            ->innerJoinWith(['clientMaster.corpMaster'])
            ->where([
                ClientChargePlan::tableName() . '.valid_chk' => ClientChargePlan::VALID,
                ClientMaster::tableName() . '.valid_chk' => ClientMaster::VALID,
                CorpMaster::tableName() . '.valid_chk' => CorpMaster::VALID,
            ]);

        $sortKey = 'id';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    $sortKey => SORT_ASC,
                ]
            ]
        ]);
        return $dataProvider;
    }

    /**
     * csvダウンロード用にファイル名を生成
     * @return string
     */
    public function csvFileName()
    {
        $csvFileName = self::tableName() . 'List_' . date('YmdHi') . '.csv';
        return $csvFileName;
    }

    /**
     * csvダウンロード用にattributeの配列を生成する
     * @return array
     */
    public function searchkeyCsvAttributes()
    {
        return [
            'clientMaster.client_no',
            'clientMaster.client_name',
            'clientChargePlan.client_charge_plan_no',
            'clientChargePlan.plan_name',
            'clientChargePlan.period',
        ];
    }
}
