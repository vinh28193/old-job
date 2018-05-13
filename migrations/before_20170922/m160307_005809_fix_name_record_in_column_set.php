<?php

use yii\db\Migration;

/**
 * Class m160307_005809_fix_name_record_in_column_set
 * column_setのカスタムattribute部分を変更
 */
class m160307_005809_fix_name_record_in_column_set extends Migration
{
    public function safeUp()
    {
        $this->update('admin_column_set', ['column_name' => 'fullName'], ['column_name' => 'adminName']);
        $this->update('application_column_set', ['column_name' => 'fullName'], ['column_name' => 'applicationName']);
        $this->update('application_column_set', ['column_name' => 'fullNameKana'], ['column_name' => 'applicationKanaName']);
    }

    public function safeDown()
    {
    }
}
