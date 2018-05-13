<?php

use yii\db\Migration;

/**
 * Handles the creation of table `hot_job_priority`.
 */
class m170712_102755_create_hot_job_priority_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable('hot_job_priority', [
            'id' => $this->primaryKey()->comment('主キーID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'hot_job_id' => $this->integer(11)->notNull()->comment('リレーションID'),
            'item' => $this->string(30)->notNull()->comment('レイアウトの優先項目名'),
            'disp_priority' => $this->integer(11)->notNull()->comment('優先順位'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('hot_job_priority');
    }
}
