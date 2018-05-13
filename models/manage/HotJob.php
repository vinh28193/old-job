<?php

namespace app\models\manage;

use Yii;
use yii\helpers\ArrayHelper;
use proseeds\models\BaseModel;

/**
 * This is the model class for table "hot_job".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $valid_chk
 * @property string  $title
 * @property integer $disp_amount
 * @property string  $disp_type
 * @property string  $text1
 * @property string  $text2
 * @property string  $text3
 * @property string  $text4
 * @property integer $text1_length
 * @property integer $text2_length
 * @property integer $text3_length
 * @property integer $text4_length
 *
 *
 */
class HotJob extends BaseModel
{
    /** 状態 - 無効or有効 */
    const INVALID = 0;
    const VALID = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hot_job';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'valid_chk',
                    'title',
                    'disp_amount',
                    'disp_type_ids',
                    'text1_length',
                    'text2_length',
                    'text3_length',
                    'text4_length',
                ],
                'required'
            ],

            ['tenant_id', 'integer'],
            ['disp_amount', 'integer', 'max' => 12],
            ['valid_chk', 'boolean'],
            ['title', 'string', 'max' => 40],
            [['text1_length', 'text2_length', 'text3_length', 'text4_length'], 'integer', 'max' => 100],
            [['text1', 'text2', 'text3', 'text4', 'disp_type_ids'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $hotJobPriority = new HotJobPriority;

        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            // todo レイアウト機能が完成したらそちらに取り込む。公開状況でON/OFF機能はなくす。
            'valid_chk' => Yii::t('app', '公開状況'),
            'title' => Yii::t('app', 'タイトル'),
            'disp_amount' => Yii::t('app', '表示原稿数'),
            'disp_type_ids' => Yii::t('app', '表示する掲載タイプ'),
            'disp_type_label' => Yii::t('app', '表示する掲載タイプ'),
            'text1' => Yii::t('app', 'テキスト1'),
            'text2' => Yii::t('app', 'テキスト2'),
            'text3' => Yii::t('app', 'テキスト3'),
            'text4' => Yii::t('app', 'テキスト4'),
            'text1_length' => Yii::t('app', 'テキスト1の文字数上限'),
            'text2_length' => Yii::t('app', 'テキスト2の文字数上限'),
            'text3_length' => Yii::t('app', 'テキスト3の文字数上限'),
            'text4_length' => Yii::t('app', 'テキスト4の文字数上限'),

            'text' => Yii::t('app', 'テキスト'),
            'text_length' => Yii::t('app', '文字数上限'),

            'item' => $hotJobPriority->getAttributeLabel('item'),
            'disp_priority' => $hotJobPriority->getAttributeLabel('disp_priority'),
            'updated_at' => $hotJobPriority->getAttributeLabel('updated_at'),
            'random' => $hotJobPriority->getAttributeLabel('random'),
            'disp_type' => $hotJobPriority->getAttributeLabel('disp_type'),
            'disp_end_date' => $hotJobPriority->getAttributeLabel('disp_end_date'),
        ];
    }


    /**
     * 表示する掲載タイプの表記用に
     * disp_type_nameを取得する
     */
    public static function getDispTypeName()
    {
        return ArrayHelper::map(DispType::find()->select([
            'disp_type_no',
            'disp_type_name'
        ])->where(['valid_chk' => DispType::VALID])->all(), 'disp_type_no', 'disp_type_name');
    }


    /**
     * disp_typeを分割して、それぞれを配列に格納する
     */
    public static function getExplodeDispType()
    {
        $typeNum = self::find()->one();
        $typeNums = explode(',', $typeNum->disp_type_ids);

        return $typeNums;
    }


    /**
     * hot_job_priorityとのリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getHotJobPriority()
    {
        return $this->hasMany(HotJobPriority::className(), ['hot_job_id' => 'id']);
    }

    /**
     * ラベル配列を取得する
     * @return array
     */
    public static function getValidChkLabels()
    {
        return [
            self::VALID => Yii::t('app', '公開'),
            self::INVALID => Yii::t('app', '非公開'),
        ];
    }

}
