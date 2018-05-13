<?php

use yii\db\Migration;

class m161208_020303_drop_sort_column_from_wage_item extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('wage_item', 'sort');
    }

    public function safeDown()
    {
        $this->addColumn('wage_item', 'sort', $this->integer(11)->defaultValue(0)->notNull() . ' COMMENT "表示順"');
    }
}
