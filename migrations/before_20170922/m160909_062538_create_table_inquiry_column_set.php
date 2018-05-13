<?php

use yii\db\Schema;
use yii\db\Migration;

class m160909_062538_create_table_inquiry_column_set extends Migration
{
    const TABLE_NAME = 'inquiry_column_set';
    const MASTER_TABLE = 'inquiry_master';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE_NAME, [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'column_no' => 'TINYINT(3) UNSIGNED NOT NULL COMMENT "表示用主キー"',
            'column_name' => 'VARCHAR(30) NOT NULL COMMENT "' . self::MASTER_TABLE . 'のカラム"',
            'label' => $this->string() . ' NOT NULL COMMENT "項目名"',
            'data_type' => $this->string() . ' NOT NULL COMMENT "入力方法"',
            'max_length' => $this->text() . ' COMMENT "長さ"',
            'is_must' => $this->boolean() . ' DEFAULT NULL COMMENT "入力条件（必須かどうか）"',
            'is_in_list' => $this->boolean() . ' DEFAULT NULL COMMENT "検索一覧表示"',
            'is_in_search' => $this->boolean() . ' DEFAULT NULL COMMENT "検索項目表示"',
            'valid_chk' => $this->boolean() . ' NOT NULL COMMENT "公開状況"',
        ], $tableOptions . ' COMMENT="掲載の問いあわせ項目"');
        $this->createIndex('idx_' . self::TABLE_NAME . '_1_tenant_id_2_column_name_3_column_no', self::TABLE_NAME, ['tenant_id','column_name','column_no']);

        // 本番で走ると怖いためコメントアウトしました
//        $this->insertInquiryColumnSetRecords(1);
//        $this->insertInquiryColumnSetRecords(2);
    }

    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }

    public function insertInquiryColumnSetRecords($tenantId)
    {
        $this->batchInsert(
            self::TABLE_NAME,
            ['tenant_id', 'column_no', 'column_name', 'label', 'data_type', 'max_length', 'is_must', 'is_in_list', 'is_in_search', 'valid_chk'],
            [
                [$tenantId, 1, 'company_name', '企業名', 'テキスト', 200, 1, null, null, 1],
                [$tenantId, 2, 'post_name', 'ご担当者部署名', 'テキスト', 200, 0, null, null, 1],
                [$tenantId, 3, 'tanto_name', 'ご担当者名', 'テキスト', 200, 1, null, null, 1],
                [$tenantId, 4, 'job_type', '職種', 'テキスト', 200, 1, null, null, 1],
                [$tenantId, 5, 'postal_code', '郵便番号', 'テキスト', 50, 1, null, null, 1],
                [$tenantId, 6, 'address', 'ご住所', 'テキスト', 200, 1, null, null, 1],
                [$tenantId, 7, 'tel_no', 'ご連絡先電話番号', 'テキスト', 200, 1, null, null, 1],
                [$tenantId, 8, 'fax_no', 'ご連絡先FAX番号', 'テキスト', 200, 0, null, null, 1],
                [$tenantId, 9, 'mail_address', 'ご連絡先メールアドレス', 'メールアドレス', 200, null, null, null, 1],
                [$tenantId, 10, 'option100', 'お問い合わせ内容', 'テキスト', 1000, 1, null, null, 1],
                [$tenantId, 11, 'option101', '募集内容', 'テキスト', 500, 0, null, null, 1],
                [$tenantId, 12, 'option102', 'その他、ご質問など', 'テキスト', 500, 0, null, null, 1],
                [$tenantId, 13, 'option103', '当サイトをどこでお知りになりましたか', 'ラジオボタン', 100, 0, null, null, 1],
                [$tenantId, 14, 'option104', 'オプション4', 'ラジオボタン', 500, 0, null, null, 1],
                [$tenantId, 15, 'option105', 'オプション5', 'テキスト', 200, 0, null, null, 1],
                [$tenantId, 16, 'option106', 'オプション6', 'テキスト', 2000, 0, null, null, 1],
                [$tenantId, 17, 'option107', 'オプション7', 'テキスト', 200, 0, null, null, 1],
                [$tenantId, 18, 'option108', 'オプション8', 'テキスト', 60, 0, null, null, 1],
                [$tenantId, 19, 'option109', 'オプション9', 'テキスト', 200, 0, null, null, 1],
            ]
        );
    }
}
