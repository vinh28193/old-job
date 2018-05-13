<?php

use yii\db\Migration;

/**
 * Class m170327_131111_add_principal_flg_column_into_searchkey_master
 * 優先キーフラグを検索キーマスターテーブルに追加する
 * 初期優先キーはsearchkey_category1
 */
class m170327_131111_add_principal_flg_column_into_searchkey_master extends Migration
{
    public function safeUp()
    {
        $this->addColumn('searchkey_master', 'principal_flg', $this->boolean()->comment('優先キーフラグ'));
        $this->update('searchkey_master', ['principal_flg' => 1], ['table_name' => 'searchkey_category1']);

        foreach (range(2, 10) as $i) {
            $tableNames[] = 'searchkey_category' . $i;
        }
        $this->update('searchkey_master', ['principal_flg' => 0], ['table_name' => $tableNames]);
    }

    public function safeDown()
    {
        $this->dropColumn('searchkey_master', 'principal_flg');
    }
}
