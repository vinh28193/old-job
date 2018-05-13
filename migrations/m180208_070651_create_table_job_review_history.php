<?php

use yii\db\Migration;

class m180208_070651_create_table_job_review_history extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB ROW_FORMAT=DYNAMIC';
        }

        // 審査履歴テーブル
        $this->createTable('job_review_history', [
            'id' => $this->primaryKey()->notNull()->comment('ID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'job_master_id' => $this->integer(11)->notNull()->comment('求人ID'),
            'admin_master_id' => $this->integer(11)->notNull()->comment('管理者ID'),
            'job_review_status_id' => $this->integer(11)->notNull()->comment('審査ステータスID'),
            'comment' => $this->string(500)->comment('コメント'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('登録日'),
        ], $tableOptions);
        $this->addCommentOnTable('job_review_history', '審査履歴');
    }

    public function safeDown()
    {
        $this->dropTable('job_review_history');
    }
}
