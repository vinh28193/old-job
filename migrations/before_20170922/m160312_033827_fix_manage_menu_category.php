<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m160312_033827_fix_manage_menu_category
 * manage_menu_categoryのテーブル構造を修正
 */
class m160312_033827_fix_manage_menu_category extends Migration
{
    const RECORD_PAR_TENANT = 12;

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $records = (new Query)->select('*')->from('manage_menu_category')->all();

        $this->dropTable('manage_menu_category');
        $this->createTable('manage_menu_category', [
            'id' => $this->primaryKey() . ' COMMENT "主キーID"',
            'tenant_id' => $this->integer()->notNull() . ' COMMENT "テナントID"',
            'title' => $this->string(20) . ' COMMENT "管理メニュー大項目名"',
            'sort' => $this->smallInteger() . ' COMMENT "表示順"',
            'icon_key' => $this->string(20) . ' COMMENT "アイコン表示用class"',
            'valid_chk' => $this->boolean()->notNull()->defaultValue(0) . ' COMMENT "状態"',
        ], $tableOptions . ' COMMENT="管理メニュー大項目"');
        $this->createIndex('idx_manage_menu_category_1_tenant_id_2_sort', 'manage_menu_category', ['tenant_id', 'sort']);

        $this->restoreRecords($records, 1);
        $this->restoreRecords($records, 2);
    }

    public function safeDown()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->dropTable('manage_menu_category');
        $this->createTable('manage_menu_category', [
            'manage_menu_category_id' => $this->primaryKey(),
            'title' => $this->text()->notNull(),
            'class' => $this->text()->notNull(),
            'href' => $this->text()->notNull(),
            'valid_chk' => $this->smallInteger()->defaultValue(0)->notNull(),
            'sort' => $this->smallInteger()->defaultValue(0)->notNull(),
            'icon_key' => $this->string(),
            'tenant_id' => $this->integer(),
        ], $tableOptions);
        $this->createIndex('manage_menu_category_PKI', 'manage_menu_category', ['manage_menu_category_id']);
    }

    public function restoreRecords($records, $tenantId)
    {
        foreach ($records as $index => $record) {
            $this->insert('manage_menu_category', [
                'id' => self::RECORD_PAR_TENANT * ($tenantId - 1) + $index + 1,
                'tenant_id' => $tenantId,
                'title' => $record['title'],
                'sort' => $record['sort'],
                'icon_key' => $record['icon_key'],
                'valid_chk' => 1,
            ]);
        }
    }
}