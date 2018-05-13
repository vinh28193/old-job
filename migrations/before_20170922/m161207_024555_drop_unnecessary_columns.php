<?php

use yii\db\Migration;

/**
 * Handles the dropping for unnecessary column.
 */
class m161207_024555_drop_unnecessary_columns extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // application_master
        $this->dropColumn('application_master','member_master_id');
        $this->dropColumn('application_master','mail_address_flg');
        $this->dropColumn('application_master','oiwai_status');
        $this->dropColumn('application_master','oiwai_pass');
        $this->dropColumn('application_master','oiwai_price');
        $this->dropColumn('application_master','disp_price');
        $this->dropColumn('application_master','admit_date');
        $this->dropColumn('application_master','admit_status');
        $this->dropColumn('application_master','first_admit_date');

        // application_master_backup
        $this->dropColumn('application_master_backup','member_master_id');
        $this->dropColumn('application_master_backup','mail_address_flg');
        $this->dropColumn('application_master_backup','oiwai_status');
        $this->dropColumn('application_master_backup','oiwai_pass');
        $this->dropColumn('application_master_backup','oiwai_price');
        $this->dropColumn('application_master_backup','disp_price');
        $this->dropColumn('application_master_backup','admit_date');
        $this->dropColumn('application_master_backup','admit_status');
        $this->dropColumn('application_master_backup','first_admit_date');

        // application_status
        $this->dropColumn('application_status','display_order');

        // client_charge
        $this->dropColumn('client_charge','disp_end_date');

        // client_charge_plan
        $this->dropColumn('client_charge_plan','oiwai_price_type');
        $this->dropColumn('client_charge_plan','oiwai_price');
        $this->dropColumn('client_charge_plan','oiwai_price_from');
        $this->dropColumn('client_charge_plan','oiwai_price_to');

        // job_master
        $this->dropColumn('job_master','medium_application_pc_url');
        $this->dropColumn('job_master','medium_application_sm_url');
        $this->dropColumn('job_master','manager_memo');
        $this->dropColumn('job_master','sample_pict_flg_1');
        $this->dropColumn('job_master','sample_pict_flg_2');
        $this->dropColumn('job_master','sample_pict_flg_3');
        $this->dropColumn('job_master','sample_pict_flg_4');
        $this->dropColumn('job_master','sample_pict_flg_5');
        $this->dropColumn('job_master','oiwai_price');

        // job_master_backup
        $this->dropColumn('job_master_backup','medium_application_pc_url');
        $this->dropColumn('job_master_backup','medium_application_sm_url');
        $this->dropColumn('job_master_backup','manager_memo');
        $this->dropColumn('job_master_backup','sample_pict_flg_1');
        $this->dropColumn('job_master_backup','sample_pict_flg_2');
        $this->dropColumn('job_master_backup','sample_pict_flg_3');
        $this->dropColumn('job_master_backup','sample_pict_flg_4');
        $this->dropColumn('job_master_backup','sample_pict_flg_5');
        $this->dropColumn('job_master_backup','oiwai_price');

        //manage_menu_main
        $this->dropColumn('manage_menu_main','manage_menu_main_id');

        // send_mail_set
        $this->dropColumn('send_mail_set','default_contents');

        // site_master
        $this->dropColumn('site_master','site_master_id');
        $this->dropColumn('site_master','company_name');
        $this->dropColumn('site_master','tanto_name');
        $this->dropColumn('site_master','support_tel_no');
        $this->dropColumn('site_master','site_url');
        $this->dropColumn('site_master','meta_description');
        $this->dropColumn('site_master','meta_keywords');
        $this->dropColumn('site_master','support_mail_name');
        $this->dropColumn('site_master','support_mail_address');
        $this->dropColumn('site_master','support_mail_subject');
        $this->dropColumn('site_master','application_mail_name');
        $this->dropColumn('site_master','application_mail_subject');
        $this->dropColumn('site_master','regist_mail_name');
        $this->dropColumn('site_master','regist_mail_address');
        $this->dropColumn('site_master','regist_mail_subject');
        $this->dropColumn('site_master','password_mail_name');
        $this->dropColumn('site_master','password_mail_address');
        $this->dropColumn('site_master','password_mail_subject');
        $this->dropColumn('site_master','expo_mail_name');
        $this->dropColumn('site_master','expo_mail_address');
        $this->dropColumn('site_master','expo_mail_subject');
        $this->dropColumn('site_master','job_mail_name');
        $this->dropColumn('site_master','job_mail_address');
        $this->dropColumn('site_master','job_mail_subject');
        $this->dropColumn('site_master','friend_mail_name');
        $this->dropColumn('site_master','friend_mail_address');
        $this->dropColumn('site_master','friend_mail_subject');
        $this->dropColumn('site_master','mail_sign');
        $this->dropColumn('site_master','review_required');
        $this->dropColumn('site_master','review_mail_name');
        $this->dropColumn('site_master','review_mail_address');
        $this->dropColumn('site_master','review_mail_subject');
        $this->dropColumn('site_master','application_required');
        $this->dropColumn('site_master','oiwai_required');
        $this->dropColumn('site_master','oiwai_mail_name');
        $this->dropColumn('site_master','oiwai_mail_address');
        $this->dropColumn('site_master','oiwai_mail_subject');
        $this->dropColumn('site_master','webmail_required');
        $this->dropColumn('site_master','scout_use');
        $this->dropColumn('site_master','member_use');
        $this->dropColumn('site_master','smart_site_title');
        $this->dropColumn('site_master','smart_meta_description');
        $this->dropColumn('site_master','smart_meta_keywords');
        $this->dropColumn('site_master','oiwai_entry_form');
        $this->dropColumn('site_master','auto_admit_required');
        $this->dropColumn('site_master','adoption_reminder_day');
        $this->dropColumn('site_master','auto_adoption_day');
        $this->dropColumn('site_master','auto_admit_day');
        $this->dropColumn('site_master','area_pref_flg');
        $this->dropColumn('site_master','medium_application_flg');
        $this->dropColumn('site_master','encryption_flg');
        $this->dropColumn('site_master','login_ssl_required');
        $this->dropColumn('site_master','oiwai_entry_deadline');
        $this->dropColumn('site_master','alert_job_num_flg');
        $this->dropColumn('site_master','alert_job_num_limit');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        // application_master
        $this->addColumn('application_master', 'member_master_id', $this->integer(11)->notNull()->comment('テーブルmember_masterのカラムid'));
        $this->addColumn('application_master','mail_address_flg',$this->bigInteger(4)->defaultValue(null)->comment("メールアドレス判別フラグ"));
        $this->addColumn('application_master','oiwai_status',$this->bigInteger(4)->notNull()->defaultValue(0)->comment("お祝い金申請状況"));
        $this->addColumn('application_master','oiwai_pass','varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT "お祝い金パスワード"');
        $this->addColumn('application_master','oiwai_price',$this->integer(11)->defaultValue(0)->comment("お祝い金金額(応募時)"));
        $this->addColumn('application_master','disp_price',$this->integer(11)->defaultValue(0) ->comment("掲載料金"));
        $this->addColumn('application_master','admit_date',$this->date()->defaultValue(null)->comment("確定日"));
        $this->addColumn('application_master','admit_status',$this->bigInteger(4)->defaultValue(null)->comment("確定状況"));
        $this->addColumn('application_master','first_admit_date',$this->date()->defaultValue(null)->comment("初回確定日"));

        // application_master_backup
        $this->addColumn('application_master_backup', 'member_master_id', $this->integer(11)->notNull()->comment('テーブルmember_masterのカラムid'));
        $this->addColumn('application_master_backup','mail_address_flg',$this->bigInteger(4)->defaultValue(null)->comment("メールアドレス判別フラグ"));
        $this->addColumn('application_master_backup','oiwai_status',$this->bigInteger(4)->notNull()->defaultValue(0)->comment("お祝い金申請状況"));
        $this->addColumn('application_master_backup','oiwai_pass','varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT "お祝い金パスワード"');
        $this->addColumn('application_master_backup','oiwai_price',$this->integer(11)->defaultValue(0)->comment("お祝い金金額(応募時)"));
        $this->addColumn('application_master_backup','disp_price',$this->integer(11)->defaultValue(0) ->comment("掲載料金"));
        $this->addColumn('application_master_backup','admit_date',$this->date()->defaultValue(null)->comment("確定日"));
        $this->addColumn('application_master_backup','admit_status',$this->bigInteger(4)->defaultValue(null)->comment("確定状況"));
        $this->addColumn('application_master_backup','first_admit_date',$this->date()->defaultValue(null)->comment("初回確定日"));

        // application_status
        $this->addColumn('application_status','display_order',$this->bigInteger(4)->defaultValue(0)->comment("表示順"));

        // client_charge
        $this->addColumn('client_charge','disp_end_date',$this->integer(11)->defaultValue(null)->comment("掲載終了日"));

        // client_charge_plan
        $this->addColumn('client_charge_plan','oiwai_price_type',$this->smallInteger(6)->notNull()->defaultValue(0)->comment('お祝い金種別  0:固定 1:範囲'));
        $this->addColumn('client_charge_plan','oiwai_price',$this->integer(11)->notNull()->defaultValue(0)->comment("お祝い金額  0:固定 1:範囲"));
        $this->addColumn('client_charge_plan','oiwai_price_from',$this->integer(11)->notNull()->defaultValue(0)->comment("お祝い金From"));
        $this->addColumn('client_charge_plan','oiwai_price_to',$this->integer(11)->notNull()->defaultValue(0)->comment("お祝い金To"));

        // job_master
        $this->addColumn('job_master', 'medium_application_pc_url', $this->text()->comment('応募媒体URL（PC)'));
        $this->addColumn('job_master', 'medium_application_sm_url', $this->text()->comment('応募媒体URL（スマホ)'));
        $this->addColumn('job_master', 'manager_memo', $this->text()->comment('管理者用備考欄'));
        $this->addColumn('job_master', 'sample_pict_flg_1', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ1'));
        $this->addColumn('job_master', 'sample_pict_flg_2', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ2'));
        $this->addColumn('job_master', 'sample_pict_flg_3', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ3'));
        $this->addColumn('job_master', 'sample_pict_flg_4', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ4'));
        $this->addColumn('job_master', 'sample_pict_flg_5', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ5'));
        $this->addColumn('job_master', 'oiwai_price', $this->integer(11)->defaultValue(null)->comment('お祝い金額'));

        // job_master_backup
        $this->addColumn('job_master_backup', 'medium_application_pc_url', $this->text()->comment('応募媒体URL（PC)'));
        $this->addColumn('job_master_backup', 'medium_application_sm_url', $this->text()->comment('応募媒体URL（スマホ)'));
        $this->addColumn('job_master_backup', 'manager_memo', $this->text()->comment('管理者用備考欄'));
        $this->addColumn('job_master_backup', 'sample_pict_flg_1', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ1'));
        $this->addColumn('job_master_backup', 'sample_pict_flg_2', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ2'));
        $this->addColumn('job_master_backup', 'sample_pict_flg_3', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ3'));
        $this->addColumn('job_master_backup', 'sample_pict_flg_4', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ4'));
        $this->addColumn('job_master_backup', 'sample_pict_flg_5', $this->smallInteger(1)->defaultValue(0)->comment('サンプル画像フラグ5'));
        $this->addColumn('job_master_backup', 'oiwai_price', $this->integer(11)->defaultValue(null)->comment('お祝い金額'));

        //manage_menu_main
        $this->addColumn('manage_menu_main','manage_menu_main_id', $this->integer(11)->notNull()->comment('管理画面少メニューID'));
        $this->createIndex('idx_manage_menu_main_manage_menu_main_id', 'manage_menu_main', ['manage_menu_main_id']);

        //send_mail_set
        $this->addColumn('send_mail_set','default_contents', $this->string(4000)->notNull()->comment('メール文面（初期値'));

        //site_master
        $this->addColumn('site_master','site_master_id', $this->integer(11)->notNull()->comment('サイトマスターID'));
        $this->addColumn('site_master','company_name', $this->string(255)->notNull()->comment('管理会社'));
        $this->addColumn('site_master','tanto_name', $this->string(255)->notNull()->comment('担当者'));
        $this->addColumn('site_master','support_tel_no', $this->string(255)->notNull()->comment('ユーザーサポート用電話番号'));
        $this->addColumn('site_master','site_url', $this->string(255)->notNull()->comment('サイトURL'));
        $this->addColumn('site_master','meta_description', $this->string(255)->notNull()->comment('PCメタタグ / description'));
        $this->addColumn('site_master','meta_keywords', $this->string(255)->notNull()->comment('PCメタタグ / keywords'));
        $this->addColumn('site_master','support_mail_name', $this->string(255)->notNull()->comment('ユーザーサポート送信用アドレス（名前）'));
        $this->addColumn('site_master','support_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','support_mail_subject', $this->string(255)->notNull()->comment('ユーザーサポート送信用アドレス（件名）'));
        $this->addColumn('site_master','application_mail_name', $this->string(255)->notNull()->comment('応募通知送信用アドレス（名前）'));
        $this->addColumn('site_master','application_mail_subject', $this->string(255)->notNull()->comment('応募通知送信用アドレス（件名）'));
        $this->addColumn('site_master','regist_mail_name', $this->string(255)->notNull()->comment('登録通知送信用アドレス（名前）'));
        $this->addColumn('site_master','regist_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','regist_mail_subject', $this->string(255)->notNull()->comment('登録通知送信用アドレス（件名）'));
        $this->addColumn('site_master','password_mail_name', $this->string(255)->notNull()->comment('パスワード再設定用アドレス（名前）'));
        $this->addColumn('site_master','password_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','password_mail_subject', $this->string(255)->notNull()->comment('パスワード再設定用アドレス（件名）'));
        $this->addColumn('site_master','expo_mail_name', $this->string(255)->notNull()->comment('説明会予約通知用アドレス（名前）'));
        $this->addColumn('site_master','expo_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','expo_mail_subject', $this->string(255)->notNull()->comment('説明会予約通知用アドレス（件名）'));
        $this->addColumn('site_master','job_mail_name', $this->string(255)->notNull()->comment('仕事情報転送用アドレス（名前）'));
        $this->addColumn('site_master','job_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','job_mail_subject', $this->string(255)->notNull()->comment('仕事情報転送用アドレス（件名）'));
        $this->addColumn('site_master','friend_mail_name', $this->string(255)->notNull()->comment('友達に紹介する（名前）'));
        $this->addColumn('site_master','friend_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','friend_mail_subject', $this->string(255)->notNull()->comment('友達に紹介する（件名）'));
        $this->addColumn('site_master','mail_sign', $this->text()->notNull()->comment('メールの署名'));
        $this->addColumn('site_master','review_required', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('審査機能'));
        $this->addColumn('site_master','review_mail_name', $this->string(255)->notNull()->comment('審査通知メール送信用アドレス（名前）'));
        $this->addColumn('site_master','review_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','review_mail_subject', $this->string(255)->notNull()->comment('審査通知メール送信用アドレス（件名）'));
        $this->addColumn('site_master','application_required', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('応募上限機能'));
        $this->addColumn('site_master','oiwai_required', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('お祝い金機能'));
        $this->addColumn('site_master','oiwai_mail_name', $this->string(255)->notNull()->comment('お祝い金申請送信用アドレス（名前）'));
        $this->addColumn('site_master','oiwai_mail_address', $this->string(32)->defaultValue(null));
        $this->addColumn('site_master','oiwai_mail_subject', $this->string(255)->notNull()->comment('お祝い金申請送信用アドレス（件名）'));
        $this->addColumn('site_master','webmail_required', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('WEBメール機能'));
        $this->addColumn('site_master','scout_use', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('スカウトメール機能'));
        $this->addColumn('site_master','member_use', $this->smallInteger(1)->notNull()->defaultValue(1)->comment('会員機能'));
        $this->addColumn('site_master','smart_site_title', $this->string(255)->notNull()->comment('スマホサイトタイトル'));
        $this->addColumn('site_master','smart_meta_description', $this->string(255)->notNull()->comment('スマホメタタグ / description'));
        $this->addColumn('site_master','smart_meta_keywords', $this->string(255)->notNull()->comment('スマホメタタグ / keywords'));
        $this->addColumn('site_master','oiwai_entry_form', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('お祝い金申込フォーム'));
        $this->addColumn('site_master','auto_admit_required', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('自動承認機能'));
        $this->addColumn('site_master','adoption_reminder_day', $this->integer(11)->notNull()->defaultValue(0)->comment('採用課金自動採用前リマインダー日数'));
        $this->addColumn('site_master','auto_adoption_day', $this->integer(11)->notNull()->defaultValue(0)->comment('採用課金自動採用日数'));
        $this->addColumn('site_master','auto_admit_day', $this->integer(11)->notNull()->defaultValue(0)->comment('応募課金自動承認日数'));
        $this->addColumn('site_master','area_pref_flg', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('47都道府県機能'));
        $this->addColumn('site_master','medium_application_flg', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('外部応募機能'));
        $this->addColumn('site_master','encryption_flg', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('暗号化機能'));
        $this->addColumn('site_master','login_ssl_required', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('ログインSSL対応'));
        $this->addColumn('site_master','oiwai_entry_deadline', $this->integer(11)->notNull()->defaultValue(90)->comment('お祝い金申請期日'));
        $this->addColumn('site_master','alert_job_num_flg', $this->smallInteger(1)->notNull()->defaultValue(0)->comment('原稿数アラート機能'));
        $this->addColumn('site_master','alert_job_num_limit', $this->integer(11)->notNull()->defaultValue(10000)->comment('アラート上限数'));
        $this->createIndex('idx_site_master_site_master_id', 'site_master', ['site_master_id']);
    }
}
