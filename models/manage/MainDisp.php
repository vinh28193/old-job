<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\InlineValidator;

/**
 * This is the model class for table "main_disp".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $main_disp_name
 * @property integer $disp_type_id
 * @property string $column_name
 * @property integer $disp_chk
 *
 * @property JobColumnSet $jobColumnSet
 */
class MainDisp extends BaseModel
{
    const DISPLAY_NAMES = [
        'main',
        'title',
        'title_small',
        'pic1',
        'comment',
        'main2',
        'pic2',
        'comment2',
        'pr',
        'pic3',
        'pic3_text',
        'pic4',
        'pic4_text',
        'pic5',
        'pic5_text',
    ];

    /** 画像3～5の位置を示すclass */
    const PIC_PLACE = [
        'pic3' => 'photo_in01',
        'pic4' => 'photo_in02',
        'pic5' => 'photo_in03',
    ];

    /** 各MainDispのタグとオプション */
    const TAG_INFO = [
        'title' => [
            'tag' => 'p',
            'options' => ['class' => 'title'],
        ],
        'title_small' => [
            'tag' => 'p',
            'options' => ['class' => 'copy'],
        ],
        'main' => [
            'tag' => 'h2',
            'options' => ['class' => 'mod-h9'],
        ],
        'comment' => [
            'tag' => 'p',
            'options' => ['class' => 'excerpt'],
        ],
        'main2' => [
            'tag' => 'h2',
            'options' => ['class' => 'mod-h10'],
        ],
        'comment2' => [
            'tag' => 'p',
            'options' => ['class' => 'excerpt'],
        ],
        'pr' => [
            'tag' => 'p',
            'options' => ['class' => 'excerpt'],
        ],
    ];

    /** 状態 - 有効 */
    const FLAG_VALID = 1;

    /** 状態 - 無効 */
    const FLAG_INVALID = 0;

    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'main_disp';
    }

    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return [
            [['main_disp_name', 'disp_type_id', 'disp_chk'], 'required'],
            [['disp_type_id'], 'integer'],
            [['disp_chk'], 'boolean'],
            [['main_disp_name'], 'string', 'max' => 20],
            [['column_name'], 'string', 'max' => 30],
            [
                ['main_disp_name'],
                function ($attribute, $params, $validator) {
                    if (!self::isDisplayName($this->$attribute)) {
                        $this->addError($attribute, Yii::t('app', '表示箇所が不正です'));
                    }
                },
            ],
        ];
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'main_disp_name' => Yii::t('app', '詳細メイン名'),
            'disp_type_id' => Yii::t('app', '掲載タイプ'),
            'column_name' => Yii::t('app', 'job_masterのカラム'),
            'disp_chk' => Yii::t('app', '表示チェック'),
        ];
    }

    /**
     * JobColumnSetとのrelation
     * @return \yii\db\ActiveQuery
     */
    public function getFunctionItemSet()
    {
        return $this->hasOne(JobColumnSet::className(), ['column_name' => 'column_name']);
    }

    /**
     * dispTypeIdからMainDisplayに表示する項目の情報を取得する
     * @param $dispTypeId
     * @return JobColumnSet[]
     */
    public static function items($dispTypeId)
    {
        $items = self::find()->with('jobColumnSet')->where([
            'disp_type_id' => $dispTypeId,
            'disp_chk' => 1,
        ])->all();
        $items = array_filter($items, function (self $self) {
            return $self->jobColumnSet->valid_chk ?? false;
        });

//        $items = self::find()->joinWith('jobColumnSet')->select([
//            self::tableName() . '.column_name',
//            JobColumnSet::tableName() . '.is_must',
//        ])->where([
//            self::tableName() . '.disp_type_id' => $dispTypeId,
//            self::tableName() . '.disp_chk' => 1,
//            JobColumnSet::tableName() . '.valid_chk' => 1,
//        ])->all();
        $items = ArrayHelper::index($items, 'main_disp_name');

        return ArrayHelper::getColumn($items, 'jobColumnSet');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJobColumnSet()
    {
        return $this->hasOne(JobColumnSet::className(), ['column_name' => 'column_name']);
    }


    /**
     * @param $dispTypeId
     * @return array
     */
    public static function bothItems($dispTypeId)
    {
        $bothItems = [];
        // dispTypeIdをセット
        JobColumnSet::setDispTypeId($dispTypeId);

        // インスタンス生成
        /** @var JobColumnSet[] $items */
        $items = JobColumnSet::find()->with('mainDisp')->where(['valid_chk' => JobColumnSet::VALID])->andWhere([
            'NOT IN',
            'job_column_set.column_name',
            JobColumnSet::NOT_AVAILABLE_MAIN_DISP_ITEMS,
        ])->all();
        // 振り分け
        foreach ($items as $item) {
            if ($item->mainDisp) {
                $bothItems['mainItems'][$item->mainDisp->main_disp_name] = $item;
            } else {
                $bothItems['notMainItems'][] = $item;
            }
        }
        return $bothItems;
    }

    /**
     * main_disp_nameが適切か判定する
     * @param $name
     * @return bool
     */
    public static function isDisplayName($name)
    {
        return in_array($name, self::DISPLAY_NAMES);
    }
}
