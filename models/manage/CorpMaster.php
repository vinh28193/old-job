<?php

namespace app\models\manage;

use app\common\Helper\JmUtils;
use proseeds\models\BaseModel;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "corp_master".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $corp_no
 * @property string $corp_name
 * @property string $created_at
 * @property string $tel_no
 * @property string $tanto_name
 * @property boolean $corp_review_flg
 * @property boolean $valid_chk
 * @property string $option100
 * @property string $option102
 * @property string $option103
 * @property string $option104
 * @property string $option105
 * @property string $option106
 * @property string $option107
 * @property string $option108
 * @property string $option109
 *
 * @property array $corpList
 * @property ClientMaster[] $clientMaster
 */
class CorpMaster extends BaseModel
{
    /** 状態 - 有効or無効 */
    const VALID = 1;
    const INVALID = 0;

    public function init()
    {
        parent::init();

        //todo requireのための仮データ。後々仕様を美化すべき
        $this->corp_no = 0;
    }

    /**
     * saveの前に行う処理
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && $this->isNewRecord) {
                $this->corp_no = (new Query())
                        ->select('max(corp_no)')
                        ->from(static::tableName())
                        ->scalar() + 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'corp_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rule = ArrayHelper::merge(Yii::$app->functionItemSet->corp->rules, [
            ['valid_chk', 'boolean'],
            ['id', 'integer'],
            [['valid_chk', 'corp_name'], 'required'],
            ['corp_name', 'unique'],
        ]);

        // 審査機能がONの場合
        if (Yii::$app->tenant->tenant->review_use) {
            $reviewFlgRules = [];

            // whenで実装すると必須の表示が上手く表示されないためこの形で実装
            $reviewFlgRules[] = ['corp_review_flg', 'boolean'];
            $reviewFlgRules[] = ['corp_review_flg', 'required'];

            // 「代理店審査中」原稿チェックルール
            // 審査なしに変更するときのみチェックする
            $reviewFlgRules[] =[
                'corp_review_flg',
                function ($attribute, $params, $validator) {
                    $clientIds = ClientMaster::find()->select('id')->where(['corp_master_id' => $this->id, 'valid_chk' => self::VALID])->column();
                    $jobNoLists = JobMaster::find()->select('job_no')->where(['client_master_id' => $clientIds, 'job_review_status_id' => JobReviewStatus::STEP_CORP_REVIEW])->column();
                    if (count($jobNoLists) > 0) {
                        $jobNoLabel = Yii::$app->functionItemSet->job->attributeLabels['job_no'];
                        $validator->addError($this, $attribute, Yii::t('app', '代理店審査中の原稿が存在するため変更できません。対象{jobNoLabel}：{jobNoList}', ['jobNoLabel' => $jobNoLabel, 'jobNoList' => implode(', ', $jobNoLists)]));
                    }
                },
                'when' => function ($model) {
                    return $model->corp_review_flg == self::INVALID && !$model->isNewRecord;
                },
            ];

            return ArrayHelper::merge($rule, $reviewFlgRules);
        }
        return $rule;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->corp->attributeLabels, [
            'corp_review_flg' => Yii::t('app', '代理店審査'),
            'valid_chk' => Yii::t('app', '取引状態'),
        ]);
    }

    /**
     * @return array
     */
    public function getFormatTable()
    {
        return [
            'valid_chk' => [self::VALID => Yii::t('app', '有効'), self::INVALID => Yii::t('app', '無効')],
            'corp_review_flg' => [self::VALID => Yii::t('app', 'あり'), self::INVALID => Yii::t('app', 'なし')],
        ];
    }

    /**
     * 状態の名前を取得します。(セットされていない場合null)
     * @return string 状態名
     */
    public function getValidChkName()
    {
        if (!isset($this->attributes['valid_chk'])) {
            return null;
        }
        return $this->attributes['valid_chk'] == self::INVALID ? Yii::t('app', '無効') : Yii::t('app', '有効');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientMaster()
    {
        return $this->hasMany(ClientMaster::className(), ['corp_master_id' => 'id']);
    }

    /**
     * ドロップダウン用リストを取得
     * 初期値ラベルにnullやfalseや空文字を入れると初期選択が無くなる
     * @param string $defaultLabel
     * @param null|1|0 $corpValid
     * @param null|1|0 $clientValid
     * @return array
     */
    public static function getDropDownArray($defaultLabel, $corpValid = null, $clientValid = null)
    {
        $query = self::find()->select([self::tableName() . '.id', 'corp_name'])->filterWhere([self::tableName() . '.valid_chk' => $corpValid]);
        if (!JmUtils::isEmpty($clientValid)) {
            $query->innerJoinWith('clientMaster', false)->andWhere([ClientMaster::tableName() . '.valid_chk' => $clientValid]);
        }

        $array = ArrayHelper::map($query->all(), 'id', 'corp_name');
        return $defaultLabel ? ['' => $defaultLabel] + $array : $array;
    }

    /**
     * 状態リストを取得する。
     * @return array 状態リスト
     */
    public static function getValidChkList()
    {
        return [
            self::VALID => Yii::t('app', '有効'),
            self::INVALID => Yii::t('app', '無効'),
        ];
    }
}
