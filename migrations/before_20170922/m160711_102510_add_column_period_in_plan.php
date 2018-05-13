<?php

use yii\db\Migration;

class m160711_102510_add_column_period_in_plan extends Migration
{
    public function safeUp()
    {
        $this->addColumn('client_charge_plan', 'period', $this->smallInteger(6) . ' COMMENT"有効日数"');
    }

    public function safeDown()
    {
        $this->dropColumn('client_charge_plan', 'period');
    }
}