<?php

use yii\db\Migration;

/**
 * Class m180206_024046_alter_column_tel_no_in_admin_master
 */
class m180206_024046_alter_column_tel_no_in_admin_master extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->alterColumn('admin_master', 'tel_no', $this->string(30)->comment('電話番号'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->alterColumn('admin_master', 'tel_no', $this->string(30)->notNull()->comment('電話番号'));
    }
}
