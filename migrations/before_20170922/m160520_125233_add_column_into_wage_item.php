<?php

use yii\db\Migration;

class m160520_125233_add_column_into_wage_item extends Migration
{
    public function safeUp()
    {
        $this->addColumn('wage_item','disp_price', $this->string(20)->notNull()->defaultValue('').' COMMENT \'表示金額\'');
    }

    public function safeDown()
    {
        $this->dropColumn('wage_item', 'disp_price');
    }
}
