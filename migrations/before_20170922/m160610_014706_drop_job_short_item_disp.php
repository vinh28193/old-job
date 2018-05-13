<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `job_short_item_disp`.
 */
class m160610_014706_drop_job_short_item_disp extends Migration
{
    public function safeUp()
    {
        $this->dropTable('job_short_item_disp');
        $this->dropTable('job_short_item_disp_result');
    }

    public function safeDown()
    {
        $this->createTable('job_short_item_disp',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_short_item_disp_no' => 'TINYINT(4) NOT NULL  COMMENT "仕事情報簡易表示セットナンバー"',
            'column_name' => $this->string(30)->notNull(). ' COMMENT "job_masterのカラム名"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事情報簡易表示項目"');
        $this->addPrimaryKey('pk_job_short_item_disp', 'job_short_item_disp', ['id']);
        $this->createIndex('idx_job_short_item_disp_1_tenant_id', 'job_short_item_disp', ['tenant_id']);

        $this->createTable('job_short_item_disp_result',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_short_item_disp_no' => 'TINYINT(4) NOT NULL  COMMENT "仕事情報簡易表示セットナンバー"',
            'column_name' => $this->string(30)->notNull(). ' COMMENT "job_masterのカラム名"',
            'valid_chk' => $this->boolean()->notNull(). ' COMMENT "状態"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事情報簡易表示項目(検索結果）"');
        $this->addPrimaryKey('pk_job_short_item_disp_result', 'job_short_item_disp_result', ['id']);
        $this->createIndex('idx_job_short_item_disp_result_1_tenant_id', 'job_short_item_disp_result', ['tenant_id']);
    }
}
