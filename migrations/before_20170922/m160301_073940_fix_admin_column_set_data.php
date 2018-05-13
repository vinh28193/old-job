<?php

use yii\db\Migration;

/**
 * Class m160301_073940_fix_admin_column_set_data
 * admin_column_setの固定値部分を修正
 */
class m160301_073940_fix_admin_column_set_data extends Migration
{
    public function safeUp()
    {
        $this->update('admin_column_set', ['column_name' => 'corp_master_id'], ['column_name' => 'corpMaster']);
        $this->update('admin_column_set', ['column_name' => 'client_master_id'], ['column_name' => 'clientMaster']);
        $this->update('admin_column_set', ['max_length' => 254], ['data_type' => 'メールアドレス']);
        $this->update('admin_column_set', ['max_length' => 2000], ['data_type' => 'URL']);
        $this->update('admin_column_set', ['max_length' => null], ['column_name' => [
            'admin_no',
            'corp_master_name',
            'client_master_id',
            'exceptions',
            'mail_address',
        ]]);
        $this->update('admin_column_set', ['is_must' => null], ['column_name' => [
            'admin_no',
            'corp_master_name',
            'client_master_id',
            'fullName',
            'login_id',
            'password',
            'exceptions',
            'mail_address',
        ]]);
        $this->update('admin_column_set', ['is_in_list' => null], ['column_name' => [
            'password',
            'exceptions',
        ]]);
        $this->update('admin_column_set', ['is_in_search' => null], ['column_name' => [
            'corp_master_name',
            'client_master_id',
            'password',
            'exceptions',
        ]]);
    }

    public function safeDown()
    {
    }
}
