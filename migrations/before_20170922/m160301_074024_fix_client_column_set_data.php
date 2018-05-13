<?php

use yii\db\Migration;

/**
 * Class m160301_074024_fix_client_column_set_data
 * client_column_setの固定値部分を修正
 */
class m160301_074024_fix_client_column_set_data extends Migration
{
    public function safeUp()
    {
        $this->update('client_column_set', ['column_name' => 'corp_master_id'], ['column_name' => 'corpMaster']);
        $this->update('client_column_set', ['max_length' => 254], ['data_type' => 'メールアドレス']);
        $this->update('client_column_set', ['max_length' => 2000], ['data_type' => 'URL']);
        $this->update('client_column_set', ['max_length' => null], ['column_name' => [
            'client_no',
            'corp_master_id',
        ]]);
        $this->update('client_column_set', ['is_must' => null], ['column_name' => [
            'client_no',
            'corp_master_id',
            'client_name',
        ]]);
        $this->update('client_column_set', ['freeword_search_flg' => null], ['column_name' => [
            'client_no',
            'corp_master_id',
        ]]);
    }

    public function safeDown()
    {
    }
}
