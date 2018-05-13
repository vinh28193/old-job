<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `column_valid_chk_in_station`.
 */
class m160701_024049_drop_column_valid_chk_in_station extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropColumn('station', 'valid_chk');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn('station', 'valid_chk', $this->boolean()->notNull()->defaultValue(1));
    }
}
