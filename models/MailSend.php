<?php

namespace app\models;

use yii;
use proseeds;
use proseeds\models;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mail_send".
 * proseeds/proseeds の共通化したモデルから継承
 * @property integer $id
 * @property integer $user_id
 * @property integer $tenant_id
 * @property integer $to_table
 * @property integer $mail_type_id
 * @property string $mail_title
 * @property string $mail_body
 * @property string $from_mail_address
 * @property string $from_name
 * @property integer $send_start_time
 * @property integer $send_end_time
 * @property integer $success_count
 * @property integer $failure_count
 * @property integer $send_status
 * @property integer $created_at
 * @property integer $update_at
 */
class MailSend extends proseeds\models\MailSend
{
    /** メール種別 */
    const TYPE_SEND_JOB               = 1;
    const TYPE_ADMN_CREATE            = 2;
    const TYPE_APPLY_TO_APPLICATION   = 3;
    const TYPE_APPLY_TO_ADMIN         = 4;
    const TYPE_MEMBERSHIP_TO_MEMBER   = 5;
    const TYPE_MEMBERSHIP_TO_ADMIN    = 6;
    const TYPE_MANAGE_PASS_RESET      = 7;
    const TYPE_MEMBER_PASS_RESET      = 8;
    const TYPE_INDIVIDUAL_APPLICATION = 9;
    const TYPE_INQUILY_NOTIFICATION   = 10;
    const TYPE_JOB_REVIEW             = 11;
    const TYPE_JOB_REVIEW_COMPLETE    = 12;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['mail_title', 'from_mail_address'], 'required'],
                ['user_id', 'default', 'value' => Yii::$app->user->id],
            ]
        );
    }
}
