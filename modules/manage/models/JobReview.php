<?php

namespace app\modules\manage\models;

use app\models\manage\JobMaster;
use app\models\manage\JobReviewHistory;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\manage\JobReviewStatus;

/**
 * 審査モデル
 *
 * @inheritdoc
 * @property boolean $review
 * @property boolean $corpReviewFlg
 */
class JobReview extends JobReviewHistory
{
    /** 審査シナリオ */
    const SCENARIO_REVIEW = 'review';

    /** 審査OK/NG */
    const REVIEW_OK = 1;
    const REVIEW_NG = 0;

    /** @var boolean 審査OK/NGフラグ */
    public $review;
    /** @var boolean 審査前ステータス */
    private $_preStatus = null;

    /**
     * ルール
     * @return array ルール設定
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['review'], 'required', 'on' => self::SCENARIO_REVIEW],
            [['review'], 'boolean'],
            // 審査ステータスの更新チェック
            // 別でステータスが更新されていた場合エラーとする。簡易な排他制御
            [
                'job_review_status_id',
                function ($attribute, $params, $validator) {
                    // 審査前、審査後でjob_review_status_idに入っている値が異なるため、2つ条件を記述。
                    if (($this->_preStatus !== null && $this->_preStatus != $this->jobMaster->job_review_status_id) ||
                        ($this->_preStatus === null && $this->job_review_status_id != $this->jobMaster->job_review_status_id)) {
                        $validator->addError($this, $attribute, Yii::t('app', '審査ステータスが別で更新されています。ご確認をお願いします。'));
                    }
                },
            ],
        ]);
    }

    /**
     * 要素のラベル名を設定
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'review' => Yii::t('app', '審査結果'),
        ]);
    }

    /**
     * 初期データセット
     * @param JobMaster $model
     * @param boolean $isRequest true;審査依頼 / false:審査
     */
    public function loadJob($model, $isRequest) {
        if (!$isRequest) {
            $this->scenario = self::SCENARIO_REVIEW;
        }
        $this->job_master_id = $model->id;
        $this->job_review_status_id = $model->job_review_status_id;
    }

    /**
     * loadメソッド拡張
     * 入力により値を変更するカラムがあるため。
     * @param array $data
     * @param string $formName
     * @return bool
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            // 管理者（審査実施者または審査依頼実施者）を設定
            $this->admin_master_id = Yii::$app->user->identity->id;

            // 審査依頼の場合のみ、審査フラグを強制的にOKにしておく
            // 代理店審査依頼時のステータス、メール送信先の分岐などに使用するため
            if ($this->scenario === $this::SCENARIO_DEFAULT) {
                $this->review = true;
            }

            // 審査ステータスを計算
            $this->calcReviewStatus();

            return true;
        }
        return false;
    }

    /**
     * 通知先説明を返す
     * @return string
     */
    public function notificationHint()
    {
        $mailLabel = Yii::$app->functionItemSet->job->attributeLabels['application_mail'];

        // 通知先説明
        $notificationReviewOk = '';
        $notificationReviewNg = '';
        $role = Yii::$app->user->identity->myRole;
        switch ($role) {
            case Manager::OWNER_ADMIN :
                $notificationReviewOk = Yii::t('app', '審査OK・・・原稿の{mailLabel}に通知されます。', ['mailLabel' => $mailLabel]);

                if ($this->corpReviewFlg) {
                    $notificationReviewNg = Yii::t('app', '審査NG・・・所属する代理店・原稿の{mailLabel}に通知されます。', ['mailLabel' => $mailLabel]);
                } else {
                    $notificationReviewNg = Yii::t('app', '審査NG・・・原稿の{mailLabel}に通知されます。', ['mailLabel' => $mailLabel]);
                }
                break;
            case Manager::CORP_ADMIN :
                $notificationReviewOk = Yii::t('app', '審査OK・・・運営元に通知されます。');
                $notificationReviewNg = Yii::t('app', '審査NG・・・原稿の{mailLabel}に通知されます。', ['mailLabel' => $mailLabel]);
                break;
        }

        $notificationMessage = Yii::t('app', '※通知先について') . '<br>';
        $notificationMessage .= $notificationReviewOk . '<br>';
        $notificationMessage .= $notificationReviewNg . '<br>';

        return $notificationMessage;
    }

    /**
     * TableFieldクラスtextメソッド用 ※審査画面用に拡張
     * @inheritdoc
     */
    public function fields()
    {
        /* @var $model JobReviewHistory */
        return ArrayHelper::merge(parent::fields(), [
            'job_review_status_id' =>  function ($model) {
                return $model->jobReviewStatus->name;
            },
        ]);
    }

    /**
     * 代理店審査フラグを返す
     * 必ずjob_master_idに値を入れてから呼び出すこと。
     * @return boolean 代理店審査フラグ
     */
    public function getCorpReviewFlg()
    {
        return $this->jobMaster->clientMaster->corpMaster->corp_review_flg;
    }

    /**
     * 審査ステータスを権限別、審査OK/NG別に設定
     */
    public function calcReviewStatus()
    {
        $role = Yii::$app->user->identity->myRole;
        $status = JobReviewStatus::reviewStatusByRole($role, $this->corpReviewFlg, $this->review);

        // チェック用に審査前ステータスを保持
        $this->_preStatus = $this->job_review_status_id;

        $this->job_review_status_id = $status;
    }
}
