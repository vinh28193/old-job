<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_disp".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $client_column
 * @property integer $sort_no
 * @property integer $disp_type_id
 */
class ClientDisp extends BaseModel
{

    /**
     * 有効
     */
    const FLAG_VALID = 1;

    /**
     * 無効
     */
    const FLAG_UNVALID = 0;

    /**
     * 変換をかける必要のあるカラム
     * (掲載企業名と駅名は、job_masterデータに依存するのでformatterは使用していません。)
     */
    const EXTEND_COLUMNS = [
        'client_master_id' => 'clientName',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_disp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tenant_id', 'sort_no', 'disp_type_id'], 'required'],
            [['tenant_id', 'sort_no', 'disp_type_id'], 'integer'],
            [['column_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'column_name' => Yii::t('app', '掲載企業項目カラム名'),
            'sort_no' => Yii::t('app', '表示順'),
            'disp_type_id' => Yii::t('app', '掲載タイプ'),
        ];
    }

    /**
     * 掲載タイプリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getDispType()
    {
        return $this->hasOne(DispType::className(), ['id' => 'disp_type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientColumnSet()
    {
        return $this->hasOne(ClientColumnSet::className(), ['column_name' => 'column_name']);
    }

    /**
     * 企業情報項目アイテム
     * @param int $dispTypeId 掲載タイプID
     * @return array
     */
    public static function items($dispTypeId)
    {
        $items = self::find()->with('clientColumnSet')->where([
            'disp_type_id' => $dispTypeId,
        ])->all();
        $items = array_filter($items, function (self $self) {
            return $self->clientColumnSet->valid_chk ?? false;
        });

//        $items = self::find()->joinWith('clientColumnSet')->select([
//            self::tableName() . '.column_name',
//            ClientColumnSet::tableName() . '.is_must',
//        ])->where([
//            self::tableName() . '.disp_type_id' => $dispTypeId,
//            ClientColumnSet::tableName() . '.valid_chk' => 1,
//        ])->all();
        ArrayHelper::multisort($items, 'sort_no');

        return ArrayHelper::getColumn($items, 'clientColumnSet');
    }

    /**
     * 入力の無い項目を排除する
     * @param JobColumnSet[] $items
     * @param ClientMaster $clientMaster
     * @return array
     */
    public static function removeEmptyJobAttributes($items, ClientMaster $clientMaster)
    {
        return array_filter($items, function ($item) use ($clientMaster) {
            /** @var JobColumnSet $item */
            return !JmUtils::isEmpty($clientMaster->{$item->column_name});
        });
    }

    /**
     * ClientMasterインスタンスを元にClientDisplayに表示する項目を
     * 'attribute:format'の形式で出力する
     * @param JobMaster $jobMaster
     * @return array
     */
    public static function getClientAttributesWithFormat(JobMaster $jobMaster)
    {
        $items = self::removeEmptyJobAttributes(self::items($jobMaster->clientChargePlan->disp_type_id), $jobMaster->clientMaster);
        return ArrayHelper::getColumn($items, 'columnNameWithFormat');
    }

    /**
     * @param $dispTypeId
     * @return array
     */
    public static function bothItems($dispTypeId)
    {
        $bothItems = [];
        // dispTypeIdをセット
        ClientColumnSet::setDispTypeId($dispTypeId);

        // インスタンス生成
        /** @var ClientColumnSet[] $items */
        $items = ClientColumnSet::find()->with('clientDisp')->where(['valid_chk' => ClientColumnSet::VALID])->andWhere([
            'NOT IN',
            'client_column_set.column_name',
            ClientColumnSet::NOT_AVAILABLE_CLIENT_DISP_ITEMS,
        ])->all();

        // 振り分け
        foreach ($items as $item) {
            if ($item->clientDisp) {
                $bothItems['clientItems'][] = $item;
            } else {
                $bothItems['notClientItems'][] = $item;
            }
        }
        // 並び替え
        ArrayHelper::multisort($bothItems['clientItems'], 'clientDisp.sort_no');

        return $bothItems;
    }
}
