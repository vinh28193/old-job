<?php

namespace app\models\manage;

use app\modules\manage\models\Manager;
use proseeds\models\BaseModel;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "client_master".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $client_no
 * @property integer $corp_master_id
 * @property string $client_name
 * @property string $client_name_kana
 * @property string $tel_no
 * @property string $address
 * @property string $tanto_name
 * @property string $created_at
 * @property integer $valid_chk
 * @property string $client_business_outline
 * @property string $client_corporate_url
 * @property string $admin_memo
 * @property string $option100
 * @property string $option101
 * @property string $option102
 * @property string $option103
 * @property string $option104
 * @property string $option105
 * @property string $option106
 * @property string $option107
 * @property string $option108
 * @property string $option109
 *
 * @property CorpMaster $corpMaster
 * @property ClientCharge[] $clientCharges
 *
 * @property CorpMaster $corpModel
 * @property ClientCharge[] $clientChargeModels
 *
 * @property array $limitNums
 * @property array $limitTypes
 */
class ClientMaster extends BaseModel
{
    /** 状態 - 有効or無効 */
    const VALID = 1;
    const INVALID = 0;
    /** @var int スカウトメール上限 */
    public $send_num_limit;
    /** @var int プランが選択されていない */
    public $clientChargePlan;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_master';
    }

    /**
     * 動的にclientChargeのgetterを生成する
     * @param string $name
     * @return mixed|\yii\db\ActiveQuery
     */
    public function __get($name)
    {
        if (!preg_match('/clientCharge\d+/', $name)) {
            return parent::__get($name);
        }
        return $this->getClientChargeModel(str_replace('clientCharge', '', $name));
    }

    /**
     * 保存前処理
     * 掲載企業IDをユニークに保つためテナントごとの最大値＋1を挿入。
     * @param boolean $insert 新規判定
     * @return boolean 保存結果
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && $this->isNewRecord) {
                //掲載企業番号の設定
                $this->client_no = self::find()->max('client_no') + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * loadAuthParamを追加
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate() && $this->loadAuthParam()) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->client->rules, [
            [['corp_master_id', 'client_name', 'valid_chk'], 'required'],
            ['admin_memo', 'string'],
            ['client_name', 'unique'],
            ['clientChargePlan', function ($attribute, $params) {
                if ($this->$attribute === false) {
                    $this->addError($attribute, Yii::t('app', '必ずひとつは選択してください'));
                }
            }],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->client->attributeLabels, [
            'corp_master_id' => Yii::t('app', '代理店'),
            'valid_chk' => Yii::t('app', '取引状態'),
            'admin_memo' => Yii::t('app', '運営元メモ'),
            'clientChargePlan' => Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label,
        ]);
    }

    /**
     * corp_masterとリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getCorpMaster()
    {
        return $this->hasOne(CorpMaster::className(), ['id' => 'corp_master_id']);
    }

    /**
     * 申し込みプランテーブルのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getClientCharges()
    {
        return $this->hasMany(ClientCharge::className(), ['client_master_id' => 'id']);
    }

    /**
     * リレーショナルモデルもしくは新しいモデルインスタンスを返す
     * @return CorpMaster
     */
    public function getCorpModel()
    {
        return $this->corpMaster ?: new CorpMaster();
    }

    /**
     * リレーショナルモデルもしくは空配列を返す
     * @return \yii\db\ActiveQuery
     */
    public function getClientChargeModels()
    {
        return $this->clientCharges ? ArrayHelper::index($this->clientCharges, 'client_charge_plan_id') : [];
    }

    /**
     * ClientChargeのリレーショナルモデルを動的に取得
     * @param $clientChargePlanId
     * @return mixed
     */
    public function getClientChargeModel($clientChargePlanId)
    {
        return ArrayHelper::getValue($this->clientChargeModels, $clientChargePlanId, new ClientCharge());
    }

    /**
     * 状態リストを取得する。
     * @return array 状態リスト
     */
    public static function getValidChkList()
    {
        return [
            self::VALID => Yii::t('app', '有効'),
            self::INVALID => Yii::t('app', '無効')
        ];
    }

    /**
     * ドロップダウン用リストを取得
     * 初期値ラベルにnullやfalseや空文字を入れると初期選択が無くなる
     * $corpMasterIdにnullや空文字等を入れると全件出し、falseや0を入れると1件も出さない
     * @param string $defaultLabel
     * @param null|1|0 $validChk
     * @param int|null|bool $corpMasterId
     * @return array
     */
    public static function getDropDownArray($defaultLabel, $validChk = null, $corpMasterId = null)
    {
        $query = self::find()->select(['id', 'client_name'])
            ->filterWhere(['valid_chk' => $validChk])
            ->andFilterWhere(['corp_master_id' => $corpMasterId]);
        $array = ArrayHelper::map($query->all(), 'id', 'client_name');

        return $defaultLabel ? ['' => $defaultLabel] + $array : $array;
    }

    /**
     * 権限を元に検索条件をロードする
     * @return bool
     */
    protected function loadAuthParam()
    {
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;
        switch ($identity->myRole) {
            case Manager::OWNER_ADMIN:
                return true;
                break;
            case Manager::CORP_ADMIN:
                $this->corp_master_id = $identity->corp_master_id;
                return true;
                break;
            default :
                return false;
                break;
        }
    }
}
