<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * 原稿検索キーの項目で使用するテーブルを作成する
 */
class m160329_004841_create_job_searchkey_item_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        //原稿検索キー項目
        for ($i = 1; $i <= 20; $i++) {//job_searchkey_item1~15まで作成　1~20
            $this->createTable('job_searchkey_item' . $i, [
                'id' => $this->integer()->notNull()->defaultValue(0) . " COMMENT '主キー' ",
                'tenant_id' => $this->integer()->notNull()->defaultValue(0) . " COMMENT 'テナントID' ",
                'job_id' => $this->integer()->notNull()->defaultValue(0) . " COMMENT '外部キー' ",
                'job_searchkey_item' . $i . '_id' => $this->integer()->notNull() . " COMMENT '外部キー' ",
            ], $tableOptions);
            $this->addPrimaryKey('pk_job_searchkey_item' . $i, 'job_searchkey_item' . $i, ['id', 'tenant_id']);
            $this->alterColumn('job_searchkey_item' . $i, 'id', 'INT(11) NOT NULL AUTO_INCREMENT COMMENT "ID"');
            $this->createIndex('idx_job_searchkey_item' . $i . '_tenant_id', 'job_searchkey_item' . $i, 'tenant_id');
            $this->createIndex('idx_job_searchkey_item' . $i . '_job_id', 'job_searchkey_item' . $i, 'job_id');
            $this->execute('ALTER TABLE job_searchkey_item' . $i . ' PARTITION BY HASH (tenant_id) PARTITIONS 3;');
        }

    }

    public function down()
    {
        for ($j = 1; $j <= 20; $j++) {
            $this->dropTable('job_searchkey_item' . $j);
        }
        return false;
    }
}
