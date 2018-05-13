<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `column_client_charge_id`.
 */
class m160614_125024_drop_column_client_charge_id extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('client_charge', 'client_charge_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn('client_charge', 'client_charge_id', $this->integer(11)->notNull() . ' COMMENT"掲載企業申込みプランID" AFTER tenant_id');
    }
}
