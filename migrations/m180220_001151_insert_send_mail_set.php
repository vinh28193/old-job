<?php

use yii\db\Migration;
use yii\db\Query;
use app\models\MailSend;
use app\models\manage\SendMailSet;

class m180220_001151_insert_send_mail_set extends Migration
{
    const TABLE_NAME = 'send_mail_set';

    const FROM_MAIL = 'pro-jm@pro-seeds.com';

    public function safeUp()
    {

        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $sort = (new Query)->from(self::TABLE_NAME)->where(['tenant_id' => $tenant['tenant_id']])->max('sort');
            $site = (new Query)->select('site_name')->from('site_master')->where(['tenant_id' => $tenant['tenant_id']])->one();
            $mailSign = '--------------------------------------------------------
【JobMaker2　運営事務局】
http://test.job-maker.jp
運営:株式会社JobMaker
TEL:000-111-2222
受付時間:10時-18時
Mail：JobMaker@JobMaker.jp
--------------------------------------------------------';

            /** 審査状況更新通知メール */
            $this->insert(self::TABLE_NAME, [
                'tenant_id' => $tenant['tenant_id'],
                'from_name' => $site['site_name'],
                'from_address' => self::FROM_MAIL,
                'subject' => '[SITE_NAME]審査状況が更新されました',
                'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査状況が更新されました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
                'mail_sign' => $mailSign,
                'mail_to' => 1,
                'valid_chk' => 1,
                'mail_name' => '審査状況更新通知',
                'sort' => ++$sort,
                'mail_type' => SendMailSet::MAIL_TYPE_JOB_REVIEW_MAIL,
                'mail_type_id' => MailSend::TYPE_JOB_REVIEW,
                'notification_address' => '',
                'mail_to_description' => '審査状況更新メールです。審査完了以外の審査ステータス変更時に送信されます。',
            ]);

            /** 審査状況更新通知メール */
            $this->insert(self::TABLE_NAME, [
                'tenant_id' => $tenant['tenant_id'],
                'from_name' => $site['site_name'],
                'from_address' => self::FROM_MAIL,
                'subject' => '[SITE_NAME]審査が完了しました',
                'contents' => '■■━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
　　　　　　　[SITE_NAME]　審査が完了しました
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━■■

管理画面URL
[ADMIN_SITE_URL]

管理画面から求人の審査状況をご確認ください。

■対象求人
[JOB_ID]

■審査ステータス
[JOB_REVIEW_STATUS]

■審査コメント
[JOB_REVIEW_COMMENT]
',
                'mail_sign' => $mailSign,
                'mail_to' => 1,
                'valid_chk' => 1,
                'mail_name' => '審査完了通知',
                'sort' => ++$sort,
                'mail_type' => SendMailSet::MAIL_TYPE_JOB_REVIEW_MAIL,
                'mail_type_id' => MailSend::TYPE_JOB_REVIEW_COMPLETE,
                'notification_address' => '',
                'mail_to_description' => '審査完了メールです。',
            ]);
        }
    }

    public function safeDown()
    {
        $this->delete(self::TABLE_NAME, ['mail_type' => SendMailSet::MAIL_TYPE_JOB_REVIEW_MAIL]);
    }
}
