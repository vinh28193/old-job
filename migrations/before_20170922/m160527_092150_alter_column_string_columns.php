<?php

use yii\db\Migration;

class m160527_092150_alter_column_string_columns extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('admin_column_set', 'max_length', $this->text() . ' COMMENT "文字数上限"');
        $this->alterColumn('application_column_set', 'max_length', $this->text() . ' COMMENT "文字数上限"');
        $this->alterColumn('client_column_set', 'max_length', $this->text() . ' COMMENT "文字数上限"');
        $this->alterColumn('corp_column_set', 'max_length', $this->text() . ' COMMENT "文字数上限"');
        $this->alterColumn('job_column_set', 'max_length', $this->text() . ' COMMENT "文字数上限"');
        
        $this->alterColumn('job_master', 'job_search_number', $this->text() . ' COMMENT "お仕事No"');
        $this->alterColumn('job_master', 'application_staff_name', $this->text() . ' COMMENT "受付担当者"');
        $this->alterColumn('job_master', 'corp_name_disp', $this->text() . ' COMMENT "会社名"');
        $this->alterColumn('job_master', 'application_tel_1', $this->string(30) . ' COMMENT "連絡先電話番号1"');
        $this->alterColumn('job_master', 'application_tel_2', $this->string(30) . ' COMMENT "連絡先電話番号2"');
        $this->alterColumn('job_master_backup', 'job_search_number', $this->text() . ' COMMENT "お仕事No"');
        $this->alterColumn('job_master_backup', 'application_staff_name', $this->text() . ' COMMENT "受付担当者"');
        $this->alterColumn('job_master_backup', 'corp_name_disp', $this->text() . ' COMMENT "会社名"');
        $this->alterColumn('job_master_backup', 'application_tel_1', $this->string(30) . ' COMMENT "連絡先電話番号1"');
        $this->alterColumn('job_master_backup', 'application_tel_2', $this->string(30) . ' COMMENT "連絡先電話番号2"');

        $this->alterColumn('application_master', 'address', $this->string(255) . ' COMMENT "住所"');
        $this->alterColumn('application_master', 'mail_address', $this->string(254) . ' COMMENT "メールアドレス"');
        $this->alterColumn('application_master', 'sex', $this->boolean() . ' COMMENT "性別"');
        $this->alterColumn('application_master_backup', 'address', $this->string(255) . ' COMMENT "住所"');
        $this->alterColumn('application_master_backup', 'mail_address', $this->string(254) . ' COMMENT "メールアドレス"');
        $this->alterColumn('application_master_backup', 'sex', $this->boolean() . ' COMMENT "性別"');

        $this->alterColumn('admin_master', 'mail_address', $this->string(254) . ' COMMENT "メールアドレス"');
    }

    public function safeDown()
    {
        $this->alterColumn('admin_column_set', 'max_length', $this->integer() . ' COMMENT "文字数上限"');
        $this->alterColumn('application_column_set', 'max_length', $this->integer() . ' COMMENT "文字数上限"');
        $this->alterColumn('client_column_set', 'max_length', $this->integer() . ' COMMENT "文字数上限"');
        $this->alterColumn('corp_column_set', 'max_length', $this->integer() . ' COMMENT "文字数上限"');
        $this->alterColumn('job_column_set', 'max_length', $this->integer() . ' COMMENT "文字数上限"');

        $this->alterColumn('job_master', 'job_search_number', $this->string(255) . ' COMMENT "お仕事No"');
        $this->alterColumn('job_master', 'application_staff_name', $this->string(255) . ' COMMENT "受付担当者"');
        $this->alterColumn('job_master', 'corp_name_disp', $this->string(255) . ' COMMENT "会社名"');
        $this->alterColumn('job_master', 'application_tel_1', $this->string(255) . ' COMMENT "連絡先電話番号1"');
        $this->alterColumn('job_master', 'application_tel_2', $this->string(255) . ' COMMENT "連絡先電話番号2"');
        $this->alterColumn('job_master_backup', 'job_search_number', $this->string(255) . ' COMMENT "お仕事No"');
        $this->alterColumn('job_master_backup', 'application_staff_name', $this->string(255) . ' COMMENT "受付担当者"');
        $this->alterColumn('job_master_backup', 'corp_name_disp', $this->string(255) . ' COMMENT "会社名"');
        $this->alterColumn('job_master_backup', 'application_tel_1', $this->string(255) . ' COMMENT "連絡先電話番号1"');
        $this->alterColumn('job_master_backup', 'application_tel_2', $this->string(255) . ' COMMENT "連絡先電話番号2"');

        $this->alterColumn('application_master', 'address', $this->text() . ' COMMENT "住所"');
        $this->alterColumn('application_master', 'mail_address', $this->string(32) . ' COMMENT "メールアドレス"');
        $this->alterColumn('application_master', 'sex', 'TINYINT COMMENT "性別"');
        $this->alterColumn('application_master_backup', 'address', $this->text() . ' COMMENT "住所"');
        $this->alterColumn('application_master_backup', 'mail_address', $this->string(32) . ' COMMENT "メールアドレス"');
        $this->alterColumn('application_master_backup', 'sex', 'TINYINT COMMENT "性別"');

        $this->alterColumn('admin_master', 'mail_address', $this->string(32) . ' COMMENT "メールアドレス"');
    }
}