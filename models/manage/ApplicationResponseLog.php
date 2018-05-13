<?php

namespace app\models\manage;

use yii;
use proseeds\models\BaseModel;
use yii\behaviors\TimestampBehavior;
use app;
use app\models;
use app\models\manage;
use app\models\MailSend;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "application_response_log".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $application_id
 * @property integer $admin_id
 * @property integer $application_status_id
 * @property string $log_message
 * @property integer $mail_send_id
 * @property integer $created_at
 */
class ApplicationResponseLog extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'application_response_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id'], 'required'],
            [['application_id', 'admin_id', 'application_status_id', 'mail_send_id'], 'integer'],
            ['admin_id', 'default', 'value' => Yii::$app->user->id],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t("app", "ID"),
            'tenant_id' => Yii::t("app", "テナントID"),
            'application_id' => Yii::t("app", "応募者ID"),
            'admin_id' => Yii::t("app", "管理者ID"),
            'application_status_id' => Yii::t("app", "状況"),
            'mail_send_id' => Yii::t("app", "送信メールID"),
            'created_at' => Yii::t('app', '登録時間（システム）'),
        ];
    }

    /**
     * 管理者情報
     * admin_master
     * @return ActiveQuery
     */
    public function getAdminMaster()
    {
        return $this->hasOne(AdminMaster::className(), ['id' => 'admin_id']);
    }

    /**
     * メール送信
     * mail_send
     * @return ActiveQuery
     */
    public function getMailSend()
    {
        return $this->hasOne(MailSend::className(), ['id' => 'mail_send_id']);
    }

    /**
     * 応募ステータス
     * application_status
     * @return ActiveQuery
     */
    public function getApplicationStatus()
    {
        return $this->hasOne(ApplicationStatus::className(), ['id' => 'application_status_id']);
    }
}
