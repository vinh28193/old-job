<?php

use yii\db\Schema;
use yii\db\Migration;

class m151022_123400_mod_column_confs extends Migration
{
    public function up()
    {
        //  NOT NULL DEFAULT ""をやめる
        $this->alterColumn('admin_master', 'login_id', Schema::TYPE_STRING . ' NOT NULL COMMENT "ログインID"');
        $this->alterColumn('admin_master', 'password', Schema::TYPE_STRING . ' NOT NULL COMMENT "パスワード"');

        // 改行が入らないカラムで型がTYPE_TEXTになっているものはTYPE_STRINGに修正
        $this->alterColumn('admin_master', 'name_sei', Schema::TYPE_STRING . ' COMMENT "名前(性)"');
        $this->alterColumn('admin_master', 'name_mei', Schema::TYPE_STRING . ' COMMENT "名前(名)"');
        $this->alterColumn('admin_master', 'mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "メールアドレス"');
        $this->alterColumn('function_item_set', 'item_name', Schema::TYPE_STRING . ' COMMENT "項目名"');
        $this->alterColumn('function_item_set', 'item_data_type', Schema::TYPE_STRING . ' COMMENT "入力項目形式"');
        $this->alterColumn('function_item_set', 'item_default_name', Schema::TYPE_STRING . ' COMMENT "デフォルト項目名"');
        $this->alterColumn('function_item_subset', 'function_item_subset_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "選択肢名"');
        $this->alterColumn('corp_master', 'corp_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "代理店名"');
        $this->alterColumn('corp_master', 'tanto_name', Schema::TYPE_STRING . ' COMMENT "担当者名"');
        $this->alterColumn('client_master', 'client_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "掲載企業名"');
        $this->alterColumn('client_master', 'client_name_kana', Schema::TYPE_STRING . ' COMMENT "掲載企業名カナ"');
        $this->alterColumn('client_master', 'tanto_name', Schema::TYPE_STRING . ' COMMENT "担当者名"');
        $this->alterColumn('client_master', 'client_corporate_url', Schema::TYPE_STRING . ' COMMENT "ホームページ"');
        $this->alterColumn('manage_menu_main', 'title', Schema::TYPE_STRING . ' NOT NULL COMMENT "タイトル"');
        $this->alterColumn('manage_menu_main', 'href', Schema::TYPE_STRING . ' NOT NULL COMMENT "URL"');
        $this->alterColumn('site_master', 'site_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "サイト名"');
        $this->alterColumn('site_master', 'company_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "管理会社"');
        $this->alterColumn('site_master', 'tanto_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "担当者"');
        $this->alterColumn('site_master', 'support_tel_no', Schema::TYPE_STRING . ' NOT NULL COMMENT "ユーザーサポート用電話番号"');
        $this->alterColumn('site_master', 'site_url', Schema::TYPE_STRING . ' NOT NULL COMMENT "サイトURL"');
        $this->alterColumn('site_master', 'site_title', Schema::TYPE_STRING . ' NOT NULL COMMENT "PCサイトタイトル"');
        $this->alterColumn('site_master', 'meta_description', Schema::TYPE_STRING . ' NOT NULL COMMENT "PCメタタグ / description"');
        $this->alterColumn('site_master', 'meta_keywords', Schema::TYPE_STRING . ' NOT NULL COMMENT "PCメタタグ / keywords"');
        $this->alterColumn('site_master', 'support_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "ユーザーサポート送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'support_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "ユーザーサポート送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'support_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "ユーザーサポート送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'application_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "応募通知送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'application_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "応募通知送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'application_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "応募通知送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'regist_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "登録通知送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'regist_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "登録通知送信用アドレス（メールアドレス"');
        $this->alterColumn('site_master', 'regist_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "登録通知送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'password_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "パスワード再設定用アドレス（名前）"');
        $this->alterColumn('site_master', 'password_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "パスワード再設定用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'password_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "パスワード再設定用アドレス（件名）"');
        $this->alterColumn('site_master', 'expo_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "説明会予約通知用アドレス（名前）"');
        $this->alterColumn('site_master', 'expo_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "説明会予約通知用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'expo_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "説明会予約通知用アドレス（件名）"');
        $this->alterColumn('site_master', 'job_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "仕事情報転送用アドレス（名前）"');
        $this->alterColumn('site_master', 'job_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "仕事情報転送用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'job_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "仕事情報転送用アドレス（件名）"');
        $this->alterColumn('site_master', 'friend_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "友達に紹介する（名前）"');
        $this->alterColumn('site_master', 'friend_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "友達に紹介する（メールアドレス）"');
        $this->alterColumn('site_master', 'friend_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "友達に紹介する（件名）"');
        $this->alterColumn('site_master', 'review_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "審査通知メール送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'review_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "審査通知メール送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'review_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "審査通知メール送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'oiwai_mail_name', Schema::TYPE_STRING . ' NOT NULL COMMENT "お祝い金申請送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'oiwai_mail_address', Schema::TYPE_STRING . ' NOT NULL COMMENT "お祝い金申請送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'oiwai_mail_subject', Schema::TYPE_STRING . ' NOT NULL COMMENT "お祝い金申請送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'smart_site_title', Schema::TYPE_STRING . ' NOT NULL COMMENT "スマホサイトタイトル"');
        $this->alterColumn('site_master', 'smart_meta_description', Schema::TYPE_STRING . ' NOT NULL COMMENT "スマホメタタグ / description"');
        $this->alterColumn('site_master', 'smart_meta_keywords', Schema::TYPE_STRING . ' NOT NULL COMMENT "スマホメタタグ / keywords"');
    }

    public function down()
    {
        $this->alterColumn('admin_master', 'login_id', Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "ログインID"');
        $this->alterColumn('admin_master', 'password', Schema::TYPE_STRING . ' NOT NULL DEFAULT "" COMMENT "パスワード"');

        $this->alterColumn('admin_master', 'name_sei', Schema::TYPE_TEXT . ' COMMENT "名前(性)"');
        $this->alterColumn('admin_master', 'name_mei', Schema::TYPE_TEXT . ' COMMENT "名前(名)"');
        $this->alterColumn('admin_master', 'mail_address', Schema::TYPE_TEXT . ' NOT NULL COMMENT "メールアドレス"');
        $this->alterColumn('function_item_set', 'item_name', Schema::TYPE_TEXT . ' COMMENT "項目名"');
        $this->alterColumn('function_item_set', 'item_data_type', Schema::TYPE_TEXT . ' COMMENT "入力項目形式"');
        $this->alterColumn('function_item_set', 'item_default_name', Schema::TYPE_TEXT . ' COMMENT "デフォルト項目名"');
        $this->alterColumn('function_item_subset', 'function_item_subset_name', Schema::TYPE_TEXT . ' NOT NULL COMMENT "選択肢名"');
        $this->alterColumn('corp_master', 'corp_name', Schema::TYPE_TEXT . ' NOT NULL COMMENT "代理店名"');
        $this->alterColumn('corp_master', 'tanto_name', Schema::TYPE_TEXT . ' COMMENT "担当者名"');
        $this->alterColumn('client_master', 'client_name', Schema::TYPE_TEXT . ' NOT NULL COMMENT "掲載企業名"');
        $this->alterColumn('client_master', 'client_name_kana', Schema::TYPE_TEXT . ' COMMENT "掲載企業名カナ"');
        $this->alterColumn('client_master', 'tanto_name', Schema::TYPE_TEXT . ' COMMENT "担当者名"');
        $this->alterColumn('client_master', 'client_corporate_url', Schema::TYPE_TEXT . ' COMMENT "ホームページ"');
        $this->alterColumn('manage_menu_main', 'title', Schema::TYPE_TEXT . ' NOT NULL COMMENT "タイトル"');
        $this->alterColumn('manage_menu_main', 'href', Schema::TYPE_TEXT . ' NOT NULL COMMENT "URL"');
        $this->alterColumn('site_master', 'site_name', 'TINYTEXT NOT NULL COMMENT "サイト名"');
        $this->alterColumn('site_master', 'company_name', 'TINYTEXT NOT NULL COMMENT "管理会社"');
        $this->alterColumn('site_master', 'tanto_name', 'TINYTEXT NOT NULL COMMENT "担当者"');
        $this->alterColumn('site_master', 'support_tel_no', 'TINYTEXT NOT NULL COMMENT "ユーザーサポート用電話番号"');
        $this->alterColumn('site_master', 'site_url', 'TINYTEXT NOT NULL COMMENT "サイトURL"');
        $this->alterColumn('site_master', 'site_title', 'TINYTEXT NOT NULL COMMENT "PCサイトタイトル"');
        $this->alterColumn('site_master', 'meta_description', 'TINYTEXT NOT NULL COMMENT "PCメタタグ / description"');
        $this->alterColumn('site_master', 'meta_keywords', 'TINYTEXT NOT NULL COMMENT "PCメタタグ / keywords"');
        $this->alterColumn('site_master', 'support_mail_name', 'TINYTEXT NOT NULL COMMENT "ユーザーサポート送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'support_mail_address', 'TINYTEXT NOT NULL COMMENT "ユーザーサポート送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'support_mail_subject', 'TINYTEXT NOT NULL COMMENT "ユーザーサポート送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'application_mail_name', 'TINYTEXT NOT NULL COMMENT "応募通知送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'application_mail_address', 'TINYTEXT NOT NULL COMMENT "応募通知送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'application_mail_subject', 'TINYTEXT NOT NULL COMMENT "応募通知送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'regist_mail_name', 'TINYTEXT NOT NULL COMMENT "登録通知送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'regist_mail_address', 'TINYTEXT NOT NULL COMMENT "登録通知送信用アドレス（メールアドレス"');
        $this->alterColumn('site_master', 'regist_mail_subject', 'TINYTEXT NOT NULL COMMENT "登録通知送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'password_mail_name', 'TINYTEXT NOT NULL COMMENT "パスワード再設定用アドレス（名前）"');
        $this->alterColumn('site_master', 'password_mail_address', 'TINYTEXT NOT NULL COMMENT "パスワード再設定用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'password_mail_subject', 'TINYTEXT NOT NULL COMMENT "パスワード再設定用アドレス（件名）"');
        $this->alterColumn('site_master', 'expo_mail_name', 'TINYTEXT NOT NULL COMMENT "説明会予約通知用アドレス（名前）"');
        $this->alterColumn('site_master', 'expo_mail_address', 'TINYTEXT NOT NULL COMMENT "説明会予約通知用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'expo_mail_subject', 'TINYTEXT NOT NULL COMMENT "説明会予約通知用アドレス（件名）"');
        $this->alterColumn('site_master', 'job_mail_name', 'TINYTEXT NOT NULL COMMENT "仕事情報転送用アドレス（名前）"');
        $this->alterColumn('site_master', 'job_mail_address', 'TINYTEXT NOT NULL COMMENT "仕事情報転送用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'job_mail_subject', 'TINYTEXT NOT NULL COMMENT "仕事情報転送用アドレス（件名）"');
        $this->alterColumn('site_master', 'friend_mail_name', 'TINYTEXT NOT NULL COMMENT "友達に紹介する（名前）"');
        $this->alterColumn('site_master', 'friend_mail_address', 'TINYTEXT NOT NULL COMMENT "友達に紹介する（メールアドレス）"');
        $this->alterColumn('site_master', 'friend_mail_subject', 'TINYTEXT NOT NULL COMMENT "友達に紹介する（件名）"');
        $this->alterColumn('site_master', 'review_mail_name', 'TINYTEXT NOT NULL COMMENT "審査通知メール送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'review_mail_address', 'TINYTEXT NOT NULL COMMENT "審査通知メール送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'review_mail_subject', 'TINYTEXT NOT NULL COMMENT "審査通知メール送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'oiwai_mail_name', 'TINYTEXT NOT NULL COMMENT "お祝い金申請送信用アドレス（名前）"');
        $this->alterColumn('site_master', 'oiwai_mail_address', 'TINYTEXT NOT NULL COMMENT "お祝い金申請送信用アドレス（メールアドレス）"');
        $this->alterColumn('site_master', 'oiwai_mail_subject', 'TINYTEXT NOT NULL COMMENT "お祝い金申請送信用アドレス（件名）"');
        $this->alterColumn('site_master', 'smart_site_title', 'TINYTEXT NOT NULL COMMENT "スマホサイトタイトル"');
        $this->alterColumn('site_master', 'smart_meta_description', 'TINYTEXT NOT NULL COMMENT "スマホメタタグ / description"');
        $this->alterColumn('site_master', 'smart_meta_keywords', 'TINYTEXT NOT NULL COMMENT "スマホメタタグ / keywords"');
    }
}
