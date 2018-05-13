<?php

namespace app\models;

use yii;
use proseeds;
use proseeds\models;

/**
 * This is the model class for table "mail_send_user".
 * proseeds/proseeds の共通化したモデルから継承
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $created_at
 * @property integer $mail_send_id
 * @property string $to_user_name
 * @property string $to_mail_address
 * @property string $replacement_strings
 * @property integer $to_table
 * @property integer $to_id
 */
class MailSendUser extends proseeds\models\MailSendUser
{
}
