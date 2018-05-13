<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;
/**
 * JobClipモデル
 *
 * @author Yukinori Nakamura
 */
class JobClip extends BaseModel
{
    public $job_id;
    /**
     * 代理店ID
     * @var type 
     */
    public $corp_master_id;
    
    /**
     * テーブル名の設定
     * @return string テーブル名
     */
    public static function tableName()
    {
        return 'job_clip';
    }
    
    /**
     * ルールの設定。
     * @return array ルール設定
     */
    public function rules()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->job->rules, [
            ['corp_master_id', 'integer'],
            ['client_master_id', 'safe'],
            ['valid_chk', 'integer']
        ]);
    }
    
    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(Yii::$app->functionItemSet->job->attributeLabels, [
            'job_no' => Yii::t('app', '仕事ID'),
            'corp_master_id' => Yii::t('app', '代理店'),
            'client_master_id' => Yii::t('app', '掲載企業'),
            'valid_chk' => Yii::t('app', '状態'),
            'client_charge_plan_id' => Yii::t('app', '申し込みプラン')
        ]);
    }
}
