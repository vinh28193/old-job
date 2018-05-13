<?php

use yii\db\Migration;

class m160511_110148_change_foreign_key_name_in_job_searchkey_item extends Migration
{
    public function safeUp()
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->renameColumn(
                'job_searchkey_item' . $i,
                'job_searchkey_item' . $i . '_id',
                'searchkey_item_id'
            );
            $this->renameColumn(
                'job_searchkey_item' . $i,
                'job_id',
                'job_master_id'
            );

        }
        $this->addColumn('searchkey_master', 'job_relation_table', $this->string(30)->notNull() . ' COMMENT "job_masterとの中間テーブル"');
    }

    public function safeDown()
    {
        for ($i = 1; $i <= 20; $i++) {
            $this->renameColumn('job_searchkey_item' . $i,
                'searchkey_item_id',
                'job_searchkey_item' . $i . '_id'
            );
            $this->renameColumn('job_searchkey_item' . $i,
                'job_master_id',
                'job_id'
            );
        }
        $this->dropColumn('searchkey_master', 'job_relation_table');
    }
}
