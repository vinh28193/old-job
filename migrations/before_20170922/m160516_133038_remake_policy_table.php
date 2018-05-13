<?php

use yii\db\Migration;

class m160516_133038_remake_policy_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->execute('DROP TABLE IF EXISTS policy');
        $this->createTable('policy', [
            'id' => $this->primaryKey(11) . ' COMMENT "主キーID"',
            'tenant_id' => $this->integer()->notNull() . ' COMMENT "テナントID"',
            'policy_no' => $this->integer(11)->notNull() . ' COMMENT "規約番号"',
            'policy_name' => $this->string(20)->notNull() . ' COMMENT "規約名"',
            'page_type' => $this->boolean()->defaultValue(0) . ' COMMENT "ページ"',
            'from_type' => $this->boolean()->defaultValue(0) . ' COMMENT "カテゴリ"',
            'policy' => $this->text()->notNull() . ' COMMENT "規約"',
            'valid_chk' => $this->boolean()->notNull()->defaultValue(0) . ' COMMENT "公開状況"',
        ], $tableOptions);
        $this->dropTable('policy_master');
    }

    public function down()
    {
        $this->dropTable('policy');
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable('policy_master', [
            'id' => $this->primaryKey(11)->notNull() . ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull() . ' COMMENT "テナントID"',
            'policy_master_no' => $this->integer(11)->notNull() . ' COMMENT "ポリシーナンバー"',
            'policy_name' => $this->string(255)->notNull() . ' COMMENT "ポリシー名"',
            'policy' => $this->text()->notNull() . ' COMMENT "ポリシー文"',
            'valid_chk' => $this->boolean()->defaultValue(1) . ' COMMENT "状態"',
        ], $tableOptions);
    }
}