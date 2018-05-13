<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160310_115232_fix_is_in_search_of_application_no_in_application_column_set
 * application_noをキーワード検索対象に
 */
class m160310_115232_fix_is_in_search_of_application_no_in_application_column_set extends Migration
{
    public function safeUp()
    {
        $this->update('application_column_set', ['is_in_search' => 1], ['column_name' => 'application_no']);
    }

    public function safeDown()
    {
    }
}
