<?php

use yii\db\Migration;

/**
 * Handles the creation for table `index`.
 */
class m160630_081056_create_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropIndex('idx_job_dist_job_master_id', 'job_dist');
        $this->dropIndex('idx_job_pref_job_master_id', 'job_pref');
        $this->createIndex('idx_job_dist_job_master_id_dist_id', 'job_dist', ['job_master_id', 'dist_id']);
        $this->createIndex('idx_job_pref_job_master_id_pref_id', 'job_pref', ['job_master_id', 'pref_id']);
    }

    public function safeDown()
    {
        $this->dropIndex('idx_job_dist_job_master_id_dist_id', 'job_dist');
        $this->dropIndex('idx_job_pref_job_master_id_pref_id', 'job_pref');
        $this->createIndex('idx_job_dist_job_master_id', 'job_dist', ['job_master_id']);
        $this->createIndex('idx_job_pref_job_master_id', 'job_pref', ['job_master_id']);
    }
}
