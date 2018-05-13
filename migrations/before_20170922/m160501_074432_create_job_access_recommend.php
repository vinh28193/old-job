<?php

use yii\db\Migration;

/**
 * Handles the creation for table `job_access_recommend`.
 */
class m160501_074432_create_job_access_recommend extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->execute('DROP TABLE IF EXISTS job_access_recommend');
        $this->createTable('job_access_recommend', [
            'id' => $this->primaryKey() . " COMMENT '主キー' ", // save()メソッドなどを実行しやすくするため、主キーを用意した
            'job_master_id' => $this->integer()->notNull() . " COMMENT '仕事ID' ",
            'tenant_id' => $this->integer()->notNull() . " COMMENT 'テナントID' ",
            'accessed_job_master_id_1' => $this->integer()->defaultValue(null) . " COMMENT '閲覧した求人原稿の仕事ID1' ",
            'accessed_job_master_id_2' => $this->integer()->defaultValue(null) . " COMMENT '閲覧した求人原稿の仕事ID2' ",
            'accessed_job_master_id_3' => $this->integer()->defaultValue(null) . " COMMENT '閲覧した求人原稿の仕事ID3' ",
            'accessed_job_master_id_4' => $this->integer()->defaultValue(null) . " COMMENT '閲覧した求人原稿の仕事ID4' ",
            'accessed_job_master_id_5' => $this->integer()->defaultValue(null) . " COMMENT '閲覧した求人原稿の仕事ID5' ",
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('job_access_recommend');
    }
}
