<?php

use app\models\manage\JobMaster;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

/**
 * Class m160713_063416_add_column_in_job_master
 * job_masterテーブルに、disp_type_idカラムを追加（負荷対策用）
 */
class m160713_063416_add_column_in_job_master extends Migration
{
    public function safeUp()
    {
        if (ArrayHelper::isIn('disp_type_id', JobMaster::getTableSchema()->columnNames)) {
            $this->dropColumn('job_master', 'disp_type_id');
        }
        $this->addColumn('job_master', 'disp_type_id', $this->integer(11)->notNull() . ' COMMENT "掲載タイプID"');
    }

    public function safeDown()
    {
        $this->dropColumn('job_master', 'disp_type_id');
    }
}
