<?php

use yii\db\Migration;

class m160421_014156_revert_column_name_in_dist_table extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('dist', 'dist_no', 'dist_cd');
    }

    public function safeDown()
    {
        $this->renameColumn('dist', 'dist_cd', 'dist_no');
    }
}
