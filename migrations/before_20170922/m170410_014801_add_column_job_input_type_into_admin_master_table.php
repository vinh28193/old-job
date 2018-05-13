<?php

use yii\db\Migration;

class m170410_014801_add_column_job_input_type_into_admin_master_table extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $this->addColumn('admin_master', 'job_input_type', $this->boolean()->notNull()->defaultValue(0)->comment('求人原稿入力方式'));
    }

    public function safeDown()
    {
        $this->dropColumn('admin_master', 'job_input_type');
    }

}
