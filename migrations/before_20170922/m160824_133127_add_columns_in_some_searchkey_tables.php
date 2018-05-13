<?php

use yii\db\Migration;

/**
 * Class m160824_133127_add_columns_in_some_searchkey_tables
 * pref_dist_masterとwage_categoryのカラムに検索URL表示用のカラムを追加
 */
class m160824_133127_add_columns_in_some_searchkey_tables extends Migration
{
    public function safeUp()
    {
        $this->addColumn('wage_category', 'wage_category_no', $this->integer(11)->notNull() . ' COMMENT "検索URLに表示されるID"');
        $this->addColumn('pref_dist_master', 'pref_dist_master_no', $this->integer(11)->notNull() . ' COMMENT "検索URLに表示されるID"');
    }

    public function safeDown()
    {
        $this->dropColumn('wage_category', 'wage_category_no');
        $this->dropColumn('pref_dist_master', 'pref_dist_master_no');
    }
}
