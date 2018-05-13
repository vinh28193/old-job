<?php

use yii\db\Migration;

/**
 * Class m160719_053612_add_column_in_searchkey_tables
 * searchkey_itemテーブルとsearchkey_categoryテーブルに、URL表示用IDのカラムを追加
 */
class m160719_053612_add_column_in_searchkey_tables extends Migration
{
    public function safeUp()
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->addColumn('searchkey_item' . $i, 'searchkey_item_no', $this->integer(11)->notNull() . ' COMMENT "検索URLに表示されるID"');
        }

        for ($i = 1; $i <= 10; $i++) {
            $this->addColumn('searchkey_category' . $i, 'searchkey_category_no', $this->integer(11)->notNull() . ' COMMENT "検索URLに表示されるID"');
        }
    }

    public function safeDown()
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->dropColumn('searchkey_item' . $i, 'searchkey_item_no');
        }

        for ($i = 1; $i <= 10; $i++) {
            $this->dropColumn('searchkey_category' . $i, 'searchkey_category_no');
        }
    }
}
