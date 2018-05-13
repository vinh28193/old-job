<?php

use yii\db\Migration;
use yii\db\Query;
use yii\db\Schema;

/**
 * Class m160301_041505_create_application_column_set
 * function_item_setのapplication部分を分離・解体したテーブルを作る
 */
class m160301_041505_create_application_column_set extends Migration
{
    const TABLE_NAME = 'application_column_set';
    const MANAGE_MENU_ID = 14;
    const RECORD_PAR_TENANT = 27;
    const MASTER_TABLE = 'application_master';
    const FUNCTION_NAME = '応募者';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable(self::TABLE_NAME, [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'column_no' => 'TINYINT UNSIGNED NOT NULL COMMENT "メニューID"',
            'column_name' => 'VARCHAR(30) NOT NULL COMMENT "' . self::MASTER_TABLE . 'のカラム"',
            'label' => Schema::TYPE_STRING . ' NOT NULL COMMENT "項目名"',
            'data_type' => 'VARCHAR(10) NOT NULL COMMENT "入力方法"',
            'max_length' => Schema::TYPE_INTEGER . ' COMMENT "文字数上限"',
            'is_must' => Schema::TYPE_BOOLEAN . ' COMMENT "入力条件"',
            'is_in_list' => Schema::TYPE_BOOLEAN . ' COMMENT "検索一覧表示"',
            'is_in_search' => Schema::TYPE_BOOLEAN . ' COMMENT "検索項目表示"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' NOT NULL COMMENT "公開状況"',
            'is_sync' => $this->boolean() . ' COMMENT "連携可能項目"',
            'sync_target' => $this->smallInteger(4) . ' UNSIGNED COMMENT "連携対象ID"',
        ], $tableOptions . ' COMMENT="' . self::FUNCTION_NAME . '情報項目"');
        $this->createIndex('idx_' . self::TABLE_NAME . '_1_tenant_id_2_column_name', self::TABLE_NAME, ['tenant_id', 'column_name']);

        $this->moveFunctionItemSetRecords(1);
        $this->moveFunctionItemSetRecords(2);
    }

    public function safeDown()
    {
        $this->dropTable(self::TABLE_NAME);
    }

    public function moveFunctionItemSetRecords($tenantId)
    {
        $data = (new Query)->select('*')->from('function_item_set')->where([
            'manage_menu_id' => self::MANAGE_MENU_ID,
            'tenant_id' => $tenantId,
        ])->all();

        foreach ($data as $index => $row)
            $this->insert(self::TABLE_NAME, [
                'id' => self::RECORD_PAR_TENANT * ($tenantId - 1) + $index + 1,
                'tenant_id' => $tenantId,
                'column_no' => $index + 1,
                'column_name' => $row['item_column'] ? : '',
                'label' => $row['item_name'],
                'data_type' => $row['item_data_type'],
                'max_length' => $row['item_maxlength'],
                'is_must' => $row['is_must_item'],
                'is_in_list' => $row['is_list_menu_item'],
                'is_in_search' => $row['is_search_menu_item'],
                'valid_chk' => $row['valid_chk'],
                'is_sync' => $row['is_common'],
                'sync_target' => $row['is_common_target_id'],
            ]);
    }
}
