<?php

use yii\db\Migration;

/**
 * Class m160224_090657_delete_disp_type_id_in_job_master
 * job_masterのdisp_type_idカラムを削除
 * downはレコードは再生しないので注意
 */
class m160224_090657_delete_disp_type_id_in_job_master extends Migration
{
    public $updateColumn = [
        'function_item_set' => 'item_column',
        'job_column_set' => 'column_name',
    ];

    public function safeUp()
    {
        $this->dropColumn('job_master', 'disp_type_id');
        foreach ($this->updateColumn as $table => $column) {
            $this->update($table, [$column => 'client_charge_plan_id'], [$column => 'disp_type_id']);
        }
    }

    public function safeDown()
    {
        $this->addColumn('job_master', 'disp_type_id', 'TINYINT NOT NULL COMMENT "掲載タイプ"');
        foreach ($this->updateColumn as $table => $column) {
            $this->update($table, [$column => 'disp_type_id'], [$column => 'client_charge_plan_id']);
        }
    }
}
