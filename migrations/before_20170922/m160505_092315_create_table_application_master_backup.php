<?php

use yii\db\Migration;

/**
 * Handles the creation for table `table_application_master_backup`.
 */
class m160505_092315_create_table_application_master_backup extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS application_master_backup');
        $this->createTable('application_master_backup', [
            'id' => $this->integer(11)->notNull() . ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull() . ' COMMENT "テナントID"',
            'application_no' => $this->integer(11)->notNull() . ' COMMENT "応募ナンバー"',
            'job_master_id' => $this->integer(11)->notNull() . ' COMMENT "テーブルjob_masterのカラムid"',
            'member_master_id' => $this->integer(11) . ' COMMENT "テーブルmember_masterのカラムid"',
            'name_sei' => $this->string(255) . ' COMMENT "名前(性)"',
            'name_mei' => $this->string(255) . ' COMMENT "名前(名)"',
            'kana_sei' => $this->string(255) . ' COMMENT "かな(性)"',
            'kana_mei' => $this->string(255) . ' COMMENT "かな(名)"',
            'sex' => $this->string(255) . ' COMMENT "性別"',
            'birth_date' => $this->date() . ' COMMENT "誕生日"',
            'pref_id' => $this->smallInteger(6) . ' COMMENT "都道府県コード"',
            'address' => $this->text() . ' COMMENT "住所"',
            'tel_no' => $this->string(30) . ' COMMENT "電話番号"',
            'mail_address_flg' => 'TINYINT COMMENT "メールアドレス判別フラグ"',
            'mail_address' => $this->string(255) . ' COMMENT "メールアドレス"',
            'occupation_id' => $this->integer(11) . ' COMMENT "属性"',
            'self_pr' => $this->text() . ' COMMENT "自己PR"',
            'created_at' => $this->integer(11)->notNull() . ' COMMENT "応募日時"',
            'option100' => $this->text() . ' COMMENT "オプション項目100"',
            'option101' => $this->text() . ' COMMENT "オプション項目101"',
            'option102' => $this->text() . ' COMMENT "オプション項目102"',
            'option103' => $this->text() . ' COMMENT "オプション項目103"',
            'option104' => $this->text() . ' COMMENT "オプション項目104"',
            'option105' => $this->text() . ' COMMENT "オプション項目105"',
            'option106' => $this->text() . ' COMMENT "オプション項目106"',
            'option107' => $this->text() . ' COMMENT "オプション項目107"',
            'option108' => $this->text() . ' COMMENT "オプション項目108"',
            'option109' => $this->text() . ' COMMENT "オプション項目109"',
            'application_status_id' => 'TINYINT DEFAULT 0 COMMENT "採用状況"',
            'carrier_type' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "応募機器"',
            'application_memo' => $this->text() . ' COMMENT "備考"',
            'oiwai_status' => 'TINYINT DEFAULT 0 NOT NULL COMMENT "お祝い金申請状況"',
            'oiwai_pass' => $this->string(255) . ' COMMENT "お祝い金パスワード"',
            'oiwai_price' => $this->integer(11) . ' DEFAULT 0 COMMENT "お祝い金金額(応募時)"',
            'disp_price' => $this->integer(11) . ' DEFAULT 0 COMMENT "掲載料金"',
            'admit_date' => $this->date() . ' COMMENT "確定日"',
            'admit_status' => 'TINYINT DEFAULT 0 COMMENT "確定状況"',
            'first_admit_date' => $this->date() . ' COMMENT "初回確定日"',
            'deleted_at' => $this->integer(11)->notNull() . ' COMMENT "削除日時"',
        ], 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="応募者情報完全削除前バックアップ"');
        $this->addPrimaryKey('pk_application_master_backup', 'application_master_backup', ['id', 'tenant_id']);
        $this->update('application_master', ['created_at' => time()], ['created_at' => null]);
        $this->alterColumn('application_master', 'created_at', $this->integer(11)->notNull() . ' COMMENT "応募日時"');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('application_master_backup');
    }
}
