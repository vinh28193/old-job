<?php

use yii\db\Schema;
use yii\db\Migration;

class m160421_015254_add_column_into_pref_dist_master_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('pref_dist_master','sort', $this->smallInteger()->defaultValue(null).' COMMENT \'表示順\'');
    }

    public function safeDown()
    {
        $this->dropColumn('pref_dist_master', 'sort');
    }
}
