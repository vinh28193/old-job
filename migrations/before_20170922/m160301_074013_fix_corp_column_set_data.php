<?php

use yii\db\Migration;

/**
 * Class m160301_074013_fix_corp_column_set_data
 * corp_column_setの固定値部分を修正
 */
class m160301_074013_fix_corp_column_set_data extends Migration
{
    public function safeUp()
    {
        $this->update('corp_column_set', ['max_length' => 254], ['data_type' => 'メールアドレス']);
        $this->update('corp_column_set', ['max_length' => 2000], ['data_type' => 'URL']);
        $this->update('corp_column_set', ['max_length' => null], ['column_name' => ['corp_no']]);
        $this->update('corp_column_set', ['is_must' => null], ['column_name' => [
            'corp_no',
            'corp_name',
        ]]);
    }

    public function safeDown()
    {
    }
}
