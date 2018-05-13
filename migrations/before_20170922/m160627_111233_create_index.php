<?php

use yii\db\Migration;

/**
 * Handles the creation for table `index`.
 */
class m160627_111233_create_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createIndex('idx_job_master_job_no', 'job_master', ['job_no']);
        $this->createIndex('idx_job_dist_job_master_id', 'job_dist', ['job_master_id']);
        $this->createIndex('idx_job_pref_job_master_id', 'job_pref', ['job_master_id']);
        $this->createIndex('idx_job_occupation_job_master_id', 'job_occupation', ['job_master_id']);
        $this->createIndex('idx_job_station_info_job_master_id', 'job_station_info', ['job_master_id']);
        $this->createIndex('idx_job_type_job_master_id', 'job_type', ['job_master_id']);
        $this->createIndex('idx_job_wage_job_master_id', 'job_wage', ['job_master_id']);
        $this->createIndex('idx_application_master_application_no', 'application_master', ['application_no']);
        $this->createIndex('idx_application_master_job_master_id', 'application_master', ['job_master_id']);

    }

    public function safeDown()
    {
        $this->dropIndex('idx_job_master_job_no', 'job_master');
        $this->dropIndex('idx_job_dist_job_master_id', 'job_dist');
        $this->dropIndex('idx_job_pref_job_master_id', 'job_pref');
        $this->dropIndex('idx_job_occupation_job_master_id', 'job_occupation');
        $this->dropIndex('idx_job_station_info_job_master_id', 'job_station_info');
        $this->dropIndex('idx_job_type_job_master_id', 'job_type');
        $this->dropIndex('idx_job_wage_job_master_id', 'job_wage');
        $this->dropIndex('idx_application_master_application_no', 'application_master');
        $this->dropIndex('idx_application_master_job_master_id', 'application_master');

    }
}
