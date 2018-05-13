<?php

use yii\db\Migration;
use yii\db\Query;
use app\models\manage\ManageMenuMain; //hot_job_priorityのモデルを設定する


class m170712_104025_insert_to_hot_job_priority extends Migration
{
    public function safeUp()
    {
        $hotJobs = (new Query)->select(['id', 'tenant_id'])->from('hot_job')->all();
        $items = ['updated_at', 'disp_end_date', 'disp_type', 'random'];
            foreach ($hotJobs as $hotJob) {
                for ($i = 0; $i < count($items); $i++) {
                    $this->insert('hot_job_priority', [
                        'tenant_id' => $hotJob['tenant_id'],
                        'hot_job_id' => $hotJob['id'],
                        'item' => $items[$i],
                        'disp_priority' => $i + 1,
                    ]);
                }
            }
    }

    public function safeDown()
    {
        $this->truncateTable('hot_job_priority');
    }
}
