<?php

use yii\db\Migration;

class m160430_072941_alter_column_oiwai_price_in_job_master extends Migration
{

    public function safeUp()
    {
        $this->alterColumn('job_master', 'oiwai_price', $this->integer(11) . ' COMMENT "お祝い金額"');
    }

    public function safeDown()
    {
        $this->alterColumn('job_master', 'oiwai_price', $this->integer(11)->notNull() . ' COMMENT "お祝い金額"');
    }
}
