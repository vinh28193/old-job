<?php

use yii\db\Migration;

/**
 * Class m160427_192716_mail
 * メール送信に関わるテーブルを作成
 */
class m160427_192716_mail extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->execute('DROP TABLE IF EXISTS mail_send');

        $this->createTable("mail_send", [
            'id' => $this->integer()->notNull(),
            'tenant_id' => $this->integer()->notNull()->defaultValue(0),
            'user_id' => $this->integer()->notNull()->defaultValue(0),
            'mail_type_id' => $this->integer()->notNull()->defaultValue(0),
            'entity_id' => $this->integer()->notNull()->defaultValue(0),
            'mail_title' => $this->string(200)->notNull()->defaultValue(''),
            'mail_body' => $this->text()->notNull(),
            'from_name' => $this->string(200)->notNull()->defaultValue(''),
            'from_mail_address' => $this->string(200)->notNull()->defaultValue(''),
            'bcc_mail_address' => $this->string(200),
            'send_pc_chk' => $this->boolean()->notNull()->defaultValue(0),
            'send_mobile_chk' => $this->boolean()->notNull()->defaultValue(0),
            'send_start_time' => $this->integer()->notNull()->defaultValue(0),
            'draft_chk' => $this->boolean()->notNull()->defaultValue(0),
            'send_count' => $this->integer()->notNull()->defaultValue(0),
            'no_send_count' => $this->integer()->notNull()->defaultValue(0),
            'send_status' => $this->integer()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey("mail_send_pk", "mail_send", ['id', 'tenant_id']);
        $this->alterColumn("mail_send", "id", "INT(11) NOT NULL AUTO_INCREMENT");
        $this->createIndex("ix_mail_send_1_mail_type_id_2_entity_id", "mail_send", ['mail_type_id', 'entity_id']);

        $this->execute('DROP TABLE IF EXISTS mail_send_user');
        $this->createTable("mail_send_user", [
            'tenant_id' => $this->integer()->notNull()->defaultValue(0),
            'mail_send_id' => $this->integer()->notNull()->defaultValue(0),
            'user_id' => $this->integer()->notNull()->defaultValue(0),
            'pc_mail_address' => $this->string(200),
            'mobile_mail_address' => $this->string(200),
            'send_pc_chk' => $this->boolean()->notNull()->defaultValue(0),
            'send_mobile_chk' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey("mail_send_user_pk", "mail_send_user", ['tenant_id', 'mail_send_id', 'user_id']);
        $this->createIndex("ix_mail_send_user_1_user_id", "mail_send_user", ['user_id']);

        $this->execute('DROP TABLE IF EXISTS mail_send_user_log');
        $this->createTable("mail_send_user_log", [
            'tenant_id' => $this->integer()->notNull()->defaultValue(0),
            'mail_send_id' => $this->integer()->notNull()->defaultValue(0),
            'user_id' => $this->integer()->notNull()->defaultValue(0),
            'from_user_id' => $this->integer()->notNull()->defaultValue(0),
            'from_name' => $this->string(200)->notNull()->defaultValue(''),
            'from_mail_address' => $this->string(200)->notNull()->defaultValue(''),
            'mail_title' => $this->string(200)->notNull()->defaultValue(''),
            'mail_body' => $this->text()->notNull(),
            'pc_mail_address' => $this->string(200)->notNull()->defaultValue(''),
            'mobile_mail_address' => $this->string(200)->notNull()->defaultValue(''),
            'send_pc_status' => $this->integer()->notNull()->defaultValue(0),
            'send_mobile_status' => $this->integer()->notNull()->defaultValue(0),
            'send_date' => $this->integer()->notNull()->defaultValue(0),
            'result' => $this->text(),
            'created_at' => $this->integer()->notNull(),
        ], $tableOptions);
        $this->addPrimaryKey("mail_send_user_log_pk", "mail_send_user_log", ['tenant_id', 'mail_send_id', 'user_id']);
        $this->createIndex("ix_mail_send_user_log_1_user_id", "mail_send_user_log", ['user_id']);
    }

    public function down()
    {
        $this->dropTable("mail_send");
        $this->dropTable("mail_send_user");
        $this->dropTable("mail_send_user_log");
    }
}
