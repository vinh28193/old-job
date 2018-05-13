<?php

use yii\db\Migration;

/**
 * Class m160425_093717_application_response_log
 * 応募者への応対履歴を残すテーブル[m160425_093717_application_response_log]を作成
 */
class m160425_093717_application_response_log extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->execute('DROP TABLE IF EXISTS application_response_log');

        $this->createTable('application_response_log', [
            'id' => $this->primaryKey() . ' COMMENT "主キーID"',
            'tenant_id' => $this->integer()->notNull() . " COMMENT 'テナントID'",
            'application_id' => $this->integer()->notNull() . " COMMENT '応募者ID'",
            'admin_id' => $this->integer()->notNull() . " COMMENT '管理者ID'",
            'application_status_id' => $this->smallInteger()->defaultValue(NULL) . " COMMENT '状況'",
            'mail_send_id' => $this->integer()->defaultValue(NULL) . " COMMENT '送信メールID'",
            'created_at' => $this->integer(11)->notNull() . " COMMENT '登録日時(システム)' ",
        ], $tableOptions . ' COMMENT="応募者管理履歴"');
    }

    public function down()
    {
        $this->dropTable('application_response_log');
    }
}
