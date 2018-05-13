<?php

use yii\db\Migration;

class m170511_094316_drop_is_more_search_column_from_searchkey_master extends Migration
{
    public function up()
    {
        $this->dropColumn('searchkey_master', 'is_more_search');
    }

    public function down()
    {
        $this->addColumn('searchkey_master', 'is_more_search', $this->boolean() . " COMMENT 'さらに絞り込み' ");
    }
}
