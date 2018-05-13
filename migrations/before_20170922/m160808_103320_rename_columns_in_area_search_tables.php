<?php

use yii\db\Migration;

/**
 * Class m160808_103320_rename_columns_in_area_search_tables
 * distテーブルのpref_idをpref_noに変更する
 */
class m160808_103320_rename_columns_in_area_search_tables extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('dist', 'pref_id', 'pref_no');
    }

    public function safeDown()
    {
        $this->renameColumn('dist', 'pref_no', 'pref_id');
    }
}
