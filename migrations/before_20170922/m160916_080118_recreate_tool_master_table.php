<?php

use yii\db\Migration;

class m160916_080118_recreate_tool_master_table extends Migration
{
    public function up()
    {
        $this->execute('DROP TABLE IF EXISTS tool_master');

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable('tool_master', [
            'id' => $this->primaryKey(11) . ' COMMENT "主キーID"',
            'tenant_id' => $this->integer(11)->notNull() . ' COMMENT "テナントID"',
            'tool_no' => $this->integer(11)->notNull() . ' COMMENT "タグNo"',
            'page_name' => $this->string(255)->notNull() . ' COMMENT "ページ名"',
            'title' => $this->string(255)->notNull() . ' COMMENT "title"',
            'description' => $this->string(255)->notNull() . ' COMMENT "description"',
            'keywords' => $this->string(255)->notNull() . ' COMMENT "keywords"',
            'h1' => $this->string(255)->notNull() . ' COMMENT "h1"',
        ], $tableOptions);
        $this->createIndex('idx_tool_master_tool_no', 'tool_master', 'tool_no');
    }

    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS tool_master');

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable('tool_master', [
            'tool_id' => $this->integer(11)->notNull()->defaultValue(0),
            'site_type' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'tag_type' => $this->smallInteger(6)->notNull()->defaultValue(0),
            'tag_detail' => $this->text()->notNull(),
            'valid_chk' => $this->smallInteger(6)->notNull()->defaultValue(1),
            'change_text' => $this->text()->notNull(),
            'access_url' => $this->text()->notNull(),
            'flg' => $this->integer(11)->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addPrimaryKey('pk_tool_master', 'tool_master', ['tool_id']);
        $this->createIndex('tool_master_PKI', 'tool_master', 'tool_id', true);
    }

}
