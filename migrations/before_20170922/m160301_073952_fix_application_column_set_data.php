<?php

use yii\db\Migration;

/**
 * Class m160301_073952_fix_application_column_set_data
 * application_column_setの固定値部分を修正
 */
class m160301_073952_fix_application_column_set_data extends Migration
{
    public function safeUp()
    {
        $this->update('application_column_set', ['column_name' => 'corpLabel'], ['column_name' => 'corpMaster']);
        $this->update('application_column_set', ['column_name' => 'clientLabel'], ['column_name' => 'clientMaster']);
        $this->update('application_column_set', ['max_length' => 254], ['data_type' => 'メールアドレス']);
        $this->update('application_column_set', ['max_length' => 2000], ['data_type' => 'URL']);
        $this->update('application_column_set', ['max_length' => null], ['column_name' => [
            'application_no',
            'corpLabel',
            'clientLabel',
            'sex',
            'birth_date',
            'pref_cd',
            'mail_address',
            'occupation_cd',
            'carrier_type',
            'regist_datetime',
            'status',
        ]]);
        $this->update('application_column_set', ['is_must' => null], ['column_name' => [
            'application_no',
            'corpLabel',
            'clientLabel',
            'fullName',
            'mail_address',
            'carrier_type',
            'regist_datetime',
        ]]);
        $this->update('application_column_set', ['is_in_search' => null], ['column_name' => [
            'application_no',
            'corpLabel',
            'clientLabel',
            'sex',
            'birth_date',
            'pref_cd',
            'address',
            'carrier_type',
            'regist_datetime',
            'status',
        ]]);
    }

    public function safeDown()
    {
    }
}