<?php

use yii\db\Migration;

/**
 * Class m160825_043434_rename_column_in_station
 * stationのpref_idのカラムをpref_noに変更（pref.pref_noとjoinする仕様なので紛らわしいため）
 */
class m160825_043434_rename_column_in_station extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('station', 'pref_id', 'pref_no');
    }

    public function safeDown()
    {
        $this->renameColumn('station', 'pref_no', 'pref_id');
    }
}
