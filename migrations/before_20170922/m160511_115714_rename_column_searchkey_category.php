<?php

use yii\db\Migration;

class m160511_115714_rename_column_searchkey_category extends Migration
{
    public function up()
    {
        for ($i = 1; $i <= 20; $i++) {
            //searchkey_categoryのカラム名を変更する
            $this->renameColumn('searchkey_item' . $i, 'searchkey_category' . $i . '_id', 'searchkey_category_id');
            //searchkey_item11～20のカラムを削除する
            if($i > 10){
                $this->dropColumn('searchkey_item' . $i, 'searchkey_category_id');
            }
        }
    }

    public function down()
    {
        echo "m160511_115714_rename_column_searchkey_category cannot be reverted.\n";

        return false;
    }
}
