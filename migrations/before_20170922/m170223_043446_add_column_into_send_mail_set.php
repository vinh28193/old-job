<?php

use yii\db\Migration;
use yii\db\Query;
use \app\models\MailSend;

/**
 * Class m170223_043446_add_column_into_send_mail_set
 * メール情報をsend_mail_setにまとめる改修
 * send_mail_setにカラム追加、デフォルト値入力後、site_masterのカラム削除
 */
class m170223_043446_add_column_into_send_mail_set extends Migration
{
    const ADD_TO_TABLE = 'send_mail_set';
    const DROP_FROM_TABLE = 'site_master';


    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn(self::ADD_TO_TABLE, 'notification_address', $this->string(255)->notNull()->defaultValue('')->comment('通知先メールアドレス')->after('mail_type_id'));
        $this->addColumn(self::ADD_TO_TABLE, 'mail_to_description', $this->string(255)->notNull()->defaultValue('')->comment('受信者説明')->after('mail_to'));

        $this->inputDefaultValue();

        $this->dropColumn(self::DROP_FROM_TABLE, 'application_mail_address');
    }

    public function safeDown()
    {
        $this->addColumn(self::DROP_FROM_TABLE, 'application_mail_address', $this->string(32)->defaultValue(null));

        $this->reverseCaseMailTypeApplyToAdmin();

        $this->dropColumn(self::ADD_TO_TABLE, 'notification_address');
        $this->dropColumn(self::ADD_TO_TABLE, 'mail_to_description');
    }


    /**
     * デフォルト値の入力
     */
    public function inputDefaultValue()
    {
        $this->inputNotificationAddress();
        $this->inputMailToAddress();
    }

    /**
     * notification_addressの更新
     */
    public function inputNotificationAddress()
    {
        $this->caseMailTypeApplyToAdmin();
        $this->caseMailTypeInquiryNotification();
    }

    /**
     * 応募メール（管理者宛）レコードに対しての処理
     * Site_master.application→notification_address
     */
    public function caseMailTypeApplyToAdmin()
    {
        $data = (new Query)->select('*')->from(self::DROP_FROM_TABLE)->all();

        foreach ((array)$data as $index => $row) {
            $this->update(self::ADD_TO_TABLE,
                ['notification_address' => $row['application_mail_address']],
                ['tenant_id' => $row['tenant_id'], 'mail_type_id' => MailSend::TYPE_APPLY_TO_ADMIN]
            );
        }
    }

    /**
     * 掲載の問い合わせメールレコードに対しての処理
     * （同一レコード内）from_address→notification_address
     */
    public function caseMailTypeInquiryNotification()
    {
        $data = (new Query)->select('*')->from(self::ADD_TO_TABLE)->where(['mail_type_id' => MailSend::TYPE_INQUILY_NOTIFICATION])->all();

        foreach ((array)$data as $index => $row) {
            $this->update(self::ADD_TO_TABLE, ['notification_address' => $row['from_address']], ['id' => $row['id']]);
        }
    }

    /**
     * mail_to_descriptionの更新
     * mail_type_id毎にメール受信先の説明を入力
     */
    public function inputMailToAddress()
    {
        $this->update(self::ADD_TO_TABLE, ['mail_to_description' => '転送請求したユーザーのメールアドレス'], ['mail_type_id' => MailSend::TYPE_SEND_JOB]);
        $this->update(self::ADD_TO_TABLE, ['mail_to_description' => '管理者として登録したメールアドレス'], ['mail_type_id' => MailSend::TYPE_ADMN_CREATE]);
        $this->update(self::ADD_TO_TABLE, ['mail_to_description' => '応募したユーザーのメールアドレス'], ['mail_type_id' => MailSend::TYPE_APPLY_TO_APPLICATION]);
        $this->update(self::ADD_TO_TABLE, ['mail_to_description' => '求人原稿に登録した応募先メールアドレス、メール設定で登録した通知先メールアドレス'], ['mail_type_id' => MailSend::TYPE_APPLY_TO_ADMIN]);
        $this->update(self::ADD_TO_TABLE, ['mail_to_description' => 'パスワード請求したメールアドレス'], ['mail_type_id' => MailSend::TYPE_MANAGE_PASS_RESET]);
        $this->update(self::ADD_TO_TABLE, ['mail_to_description' => '掲載の問い合わせをしたユーザーのメールアドレス、メール設定で登録した通知先メールアドレス'], ['mail_type_id' => MailSend::TYPE_INQUILY_NOTIFICATION]);
    }

    /**
     * Site_master.application_addressの更新
     * 応募メール（管理者宛）レコードのnotification_address→Site_master.application
     */
    public function reverseCaseMailTypeApplyToAdmin()
    {
        $data = (new Query)->select('*')->from(self::ADD_TO_TABLE)->where(['mail_type_id' => MailSend::TYPE_APPLY_TO_ADMIN])->all();

        foreach ((array)$data as $index => $row) {
            $this->update(self::DROP_FROM_TABLE,
                ['application_mail_address' => $row['notification_address']],
                ['tenant_id' => $row['tenant_id']]
            );
        }
    }
}
