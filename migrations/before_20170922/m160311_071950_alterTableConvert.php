<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160311_071950_alterTableConvert
 * 照合順序変更
 */
class m160311_071950_alterTableConvert extends Migration
{
    const RBAC_TABLES = [
        'auth_assignment',
        'auth_item',
        'auth_item_child',
        'auth_rule',
    ];
    const MAIL_ADDRESS_COLUMN_LIST = [
        [
            'table_name' => 'admin_master',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'application_master',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'expo_master',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'expo_reserve_log',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'member_master',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'oiwai_application',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'oiwai_master',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'send_mail_logs_application',
            'column_name' => 'from_mail_address',
        ],
        [
            'table_name' => 'send_mail_logs_scout',
            'column_name' => 'from_mail_address',
        ],
        [
            'table_name' => 'send_mail_logs_scout_subset',
            'column_name' => 'to_mail_address',
        ],
        [
            'table_name' => 'send_mail_logs_subset',
            'column_name' => 'mail_address',
        ],
        [
            'table_name' => 'send_mail_master',
            'column_name' => 'mail_from_address_mobile',
        ],
        [
            'table_name' => 'send_mail_master',
            'column_name' => 'mail_from_address_pc',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'application_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'expo_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'friend_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'job_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'oiwai_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'password_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'regist_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'review_mail_address',
        ],
        [
            'table_name' => 'site_master',
            'column_name' => 'support_mail_address',
        ],
        [
            'table_name' => 'send_mail_scout_master',
            'column_name' => 'mail_from_address_pc',
        ],
        [
            'table_name' => 'send_mail_scout_master',
            'column_name' => 'mail_from_address_mobile',
        ],
    ];

    public function up()
    {
       $tables = $this->db->createCommand('show tables;')->queryAll();
        foreach ($tables as $table) {
            $tableName = $table['Tables_in_'.$this->db->createCommand('SELECT database();')->queryScalar()];
            if (!$table = in_array($tableName, self::RBAC_TABLES)) {
                $this->execute("ALTER TABLE $tableName CONVERT TO CHARACTER SET utf8 COLLATE 'utf8_bin' ,ENGINE=InnoDB;");
            }
        }

        foreach (self::MAIL_ADDRESS_COLUMN_LIST AS $mailAddressColumn) {
            $mailTable = $mailAddressColumn['table_name'];
            $mailColumn = $mailAddressColumn['column_name'];
            $this->execute("ALTER TABLE $mailTable MODIFY COLUMN $mailColumn VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci';");
        }
    }

    public function down()
    {
        $tables = $this->db->createCommand('show tables;')->queryAll();
        foreach ($tables as $table) {

            $tableName = $table['Tables_in_'.$this->db->createCommand('SELECT database();')->queryScalar()];
            if (!$table = in_array($tableName, self::RBAC_TABLES)) {
                $this->execute("ALTER TABLE $tableName CONVERT TO CHARACTER SET utf8 COLLATE 'utf8_unicode_ci' ,ENGINE=InnoDB;");
            }
            foreach (self::MAIL_ADDRESS_COLUMN_LIST AS $mailAddressColumn) {
                $mailTable = $mailAddressColumn['table_name'];
                $mailColumn = $mailAddressColumn['column_name'];
                $this->execute("ALTER TABLE $mailTable MODIFY COLUMN $mailColumn VARCHAR(32) CHARACTER SET 'utf8' COLLATE 'utf8_bin';");
            }
        }
    }
}
