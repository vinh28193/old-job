<?php

namespace app\models\manage;

use app\modules\manage\models\Manager;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * 審査ステータスモデル（非DBモデル）
 *
 * @property integer $id ※setterのみ
 * @property string $name ※getterのみ
 */
class JobReviewStatus extends Model
{
    /** 審査ステータスID */
    const STEP_JOB_EDIT        = 1;  // 審査前（修正中）
    const STEP_CORP_REVIEW_NG  = 2;  // 代理店審査NG
    const STEP_OWNER_REVIEW_NG = 3;  // 運営元審査NG
    const STEP_CORP_REVIEW     = 4;  // 代理店審査中
    const STEP_OWNER_REVIEW    = 5;  // 本部審査中
    const STEP_REVIEW_OK       = 6;  // 審査OK

    /** @var integer $id 審査ステータスID */
    private $_id = null;

    /**
     * ラベルを返す
     * @return string ラベル
     */
    public static function attributeLabel()
    {
        return Yii::t('app', '審査ステータス');
    }

    /**
     * [id => statusName]の形でデータを返す
     *
     * @param boolean $allFlg プルダウン用に「すべて」項目をつけるかどうか
     * @return array
     */
    public static function reviewStatuses($allFlg = false)
    {
        $allArr = ['' => Yii::t('app', 'すべて')];
        $statuses = [
            self::STEP_JOB_EDIT => Yii::t('app', '審査前（修正中）'),
            self::STEP_CORP_REVIEW_NG => Yii::t('app', '代理店審査NG'),
            self::STEP_OWNER_REVIEW_NG => Yii::t('app', '運営元審査NG'),
            self::STEP_CORP_REVIEW => Yii::t('app', '代理店審査中'),
            self::STEP_OWNER_REVIEW => Yii::t('app', '運営元審査中'),
            self::STEP_REVIEW_OK => Yii::t('app', '審査完了'),
        ];

        return $allFlg ? ArrayHelper::merge($allArr, $statuses) : $statuses;
    }

    /**
     * 審査ステータス名を返す
     * @param integer $id
     * @return string
     */
    public function getName()
    {
        return ArrayHelper::getValue(static::reviewStatuses(), $this->_id, '');
    }

    /**
     * IDをセットする
     * @param integer $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * 管理者権限別、代理店審査有無別に審査対象ステータスを返す
     * 今後ステップを跨いで審査する可能性も考慮して配列で返す。
     * @param string $role
     * @param boolean $corpReviewFlg
     * @return array 審査ステータスID配列
     */
    public static function reviewTargetStatusesByRole($role, $corpReviewFlg)
    {
        $reviewStatuses = null;
        switch ($role) {
            case Manager::OWNER_ADMIN :
                $reviewStatuses = [self::STEP_OWNER_REVIEW];
                break;
            case Manager::CORP_ADMIN :
                $reviewStatuses = $corpReviewFlg ? [self::STEP_CORP_REVIEW] : [];
                break;
            case Manager::CLIENT_ADMIN :
                $reviewStatuses = [];
                break;
        }
        return $reviewStatuses;
    }

    /**
     * 管理者別、審査OK/NG別、代理店審査有無別に審査後ステータスを返す
     * @param string $role
     * @param boolean $corpReviewFlg
     * @param boolean $okFlg
     * @return integer 審査ステータスID
     */
    public static function reviewStatusByRole($role, $corpReviewFlg, $okFlg)
    {
        // 審査機能OFFの時は「審査OK」を返す
        // こちらは念のための処理
        if (!Yii::$app->tenant->tenant->review_use) {
            return self::STEP_REVIEW_OK;
        }

        $reviewStatus = null;
        switch ($role) {
            case Manager::OWNER_ADMIN :
                $reviewStatus = $okFlg ? self::STEP_REVIEW_OK : self::STEP_OWNER_REVIEW_NG;
                break;
            case Manager::CORP_ADMIN :
                $reviewStatus = $okFlg ? self::STEP_OWNER_REVIEW : self::STEP_CORP_REVIEW_NG;
                break;
            case Manager::CLIENT_ADMIN :
                $reviewStatus =  $corpReviewFlg ? self::STEP_CORP_REVIEW : self::STEP_OWNER_REVIEW;
                break;
        }
        return $reviewStatus;
    }

    /**
     * 求人原稿登録時の審査ステータスを返す
     * @param string $role
     * @return integer 審査ステータスID
     */
    public static function jobRegisterReviewStatus($role)
    {
        // 審査機能OFFの時は「審査OK」を返す
        if (!Yii::$app->tenant->tenant->review_use) {
            return self::STEP_REVIEW_OK;
        }

        $reviewStatus = null;
        switch ($role) {
            case Manager::OWNER_ADMIN :
                $reviewStatus = self::STEP_REVIEW_OK;
                break;
            case Manager::CORP_ADMIN :
                $reviewStatus = self::STEP_JOB_EDIT;
                break;
            case Manager::CLIENT_ADMIN :
                $reviewStatus = self::STEP_JOB_EDIT;
                break;
        }
        return $reviewStatus;
    }
}
