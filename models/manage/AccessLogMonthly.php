<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;

/**
 * This is the model class for table "access_log_monthly".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $accessed_at
 * @property integer $detail_count_pc
 * @property integer $detail_count_smart
 * @property integer $application_count_pc
 * @property integer $application_count_smart
 * @property integer $member_count_pc
 * @property integer $member_count_smart
 *
 * @property integer $applicationCountTotal
 * @property integer $memberCountTotal
 * @property integer $detailCountTotal
 */
class AccessLogMonthly extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_log_monthly';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'access_date',
                    'detail_count_pc',
                    'application_count_pc',
                    'member_count_pc',
                ],
                'required'
            ],
            [['access_date'], 'safe'],
            [
                [
                    'detail_count_pc',
                    'application_count_pc',
                    'member_count_pc',
                    'detail_count_smart',
                    'application_count_smart',
                    'member_count_smart',
                    'applicationCountTotal',
                    'memberCountTotal',
                    'detailCountTotal',
                ],
                'integer'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'access_date' => Yii::t('app', 'Access Date'),
            'detail_count_pc' => Yii::t('app', 'Detail Count Pc'),
            'application_count_pc' => Yii::t('app', 'Application Count Pc'),
            'member_count_pc' => Yii::t('app', 'Member Count Pc'),
            'detail_count_smart' => Yii::t('app', 'Detail Count Smart'),
            'application_count_smart' => Yii::t('app', 'Application Count Smart'),
            'member_count_smart' => Yii::t('app', 'Member Count Smart'),
            'applicationCountTotal' => Yii::t('app', ''),
            'memberCountTotal' => Yii::t('app', ''),
            'detailCountTotal' => Yii::t('app', ''),
        ];
    }

    /**
     * 応募者数合計のgetter
     * @return int|string
     */
    public function getApplicationCountTotal()
    {
        return $this->application_count_pc + $this->application_count_smart;
    }

    /**
     * 登録者数合計のgetter
     * @return int|string
     */
    public function getMemberCountTotal()
    {
        return $this->member_count_pc + $this->member_count_smart;
    }

    /**
     * 詳細画面閲覧者数合計のgetter
     * @return int|string
     */
    public function getDetailCountTotal()
    {
        return $this->detail_count_pc + $this->detail_count_smart;
    }

    /**
     * 昨日のレコードを取得する
     * @return null|self
     */
    static function findYesterdayRecord(){
        return self::find()->where(['access_date' => date('Y-m-d', strtotime('yesterday'))])->one();
    }
}
