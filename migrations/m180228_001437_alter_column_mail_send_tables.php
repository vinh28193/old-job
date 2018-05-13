<?php

use yii\db\Migration;

class m180228_001437_alter_column_mail_send_tables extends Migration
{
    /** メールアドレス最大文字数 */
    const MAIL_MAX = 254;

    public function safeUp()
    {
        // メールアドレス系カラムの文字数を合わせる
        // mail_send
        $this->alterColumn(
            'mail_send',
            'mail_title',
            $this->string(255)->comment('メールタイトル')
        );
        $this->alterColumn(
            'mail_send',
            'from_name',
            $this->string(255)->comment('送信元名')
        );
        $this->alterColumn(
            'mail_send',
            'from_mail_address',
            $this->string(self::MAIL_MAX)->comment('送信元メールアドレス')
        );
        $this->alterColumn(
            'mail_send',
            'bcc_mail_address',
            $this->string(self::MAIL_MAX)->comment('BCCメールアドレス')
        );
        $this->alterColumn(
            'mail_send',
            'from_mail_address',
            $this->string(self::MAIL_MAX)->comment('送信元メールアドレス')
        );

        // mail_send_user
        $this->alterColumn(
            'mail_send_user',
            'pc_mail_address',
            $this->string(self::MAIL_MAX)->comment('送信先PCメールアドレス')
            );
        $this->alterColumn(
            'mail_send_user',
            'mobile_mail_address',
            $this->string(self::MAIL_MAX)->comment('送信先モバイルメールアドレス')
        );


        // mail_send_user_log
        $this->alterColumn(
            'mail_send_user_log',
            'mail_title',
            $this->string(255)->comment('メールタイトル')
            );
        $this->alterColumn(
            'mail_send_user_log',
            'from_name',
            $this->string(255)->comment('送信元名')
            );
        $this->alterColumn(
            'mail_send_user_log',
            'from_mail_address',
            $this->string(self::MAIL_MAX)->comment('送信元メールアドレス')
            );
        $this->alterColumn(
            'mail_send_user_log',
            'pc_mail_address',
            $this->string(self::MAIL_MAX)->comment('送信先PCメールアドレス')
            );
        $this->alterColumn(
            'mail_send_user_log',
            'mobile_mail_address',
            $this->string(self::MAIL_MAX)->comment('送信先モバイルメールアドレス')
        );
    }

    public function safeDown()
    {
        $max = 200;

        // mail_send
        $this->alterColumn(
            'mail_send',
            'mail_title',
            $this->string($max)->comment('メールタイトル')
        );
        $this->alterColumn(
            'mail_send',
            'from_name',
            $this->string($max)->comment('送信元名')
        );
        $this->alterColumn(
            'mail_send',
            'from_mail_address',
            $this->string($max)->comment('送信元メールアドレス')
        );
        $this->alterColumn(
            'mail_send',
            'bcc_mail_address',
            $this->string($max)->comment('BCCメールアドレス')
        );
        $this->alterColumn(
            'mail_send',
            'from_mail_address',
            $this->string($max)->comment('送信元メールアドレス')
        );

        // mail_send_user
        $this->alterColumn(
            'mail_send_user',
            'pc_mail_address',
            $this->string($max)->comment('送信先PCメールアドレス')
        );
        $this->alterColumn(
            'mail_send_user',
            'mobile_mail_address',
            $this->string($max)->comment('送信先モバイルメールアドレス')
        );

        // mail_send_user_log
        $this->alterColumn(
            'mail_send_user_log',
            'mail_title',
            $this->string($max)->comment('メールタイトル')
        );
        $this->alterColumn(
            'mail_send_user_log',
            'from_name',
            $this->string($max)->comment('送信元名')
        );
        $this->alterColumn(
            'mail_send_user_log',
            'from_mail_address',
            $this->string($max)->comment('送信元メールアドレス')
        );
        $this->alterColumn(
            'mail_send_user_log',
            'pc_mail_address',
            $this->string($max)->comment('送信先PCメールアドレス')
        );
        $this->alterColumn(
            'mail_send_user_log',
            'mobile_mail_address',
            $this->string($max)->comment('送信先モバイルメールアドレス')
        );
    }
}
