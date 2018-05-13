<?php

use yii\db\Migration;

/**
 * Handles adding column_wage_item_no to table `wage_item`.
 */
class m160516_090121_add_column_wage_item_no_to_wage_item extends Migration
{
    public function safeUp()
    {
        $this->addColumn('wage_item','wage_item_no', $this->integer()->notNull().' COMMENT "給与ナンバー" AFTER wage_category_id');
    }

    public function safeDown()
    {
        $this->dropColumn('wage_item', 'wage_item_no');
    }
}
