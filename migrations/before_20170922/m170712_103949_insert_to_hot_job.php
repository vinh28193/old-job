<?php

use yii\db\Migration;
use yii\db\Query;
use app\models\manage\ManageMenuMain; //hot_jobのモデルを設定する

class m170712_103949_insert_to_hot_job extends Migration
{
    public function safeUp()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('hot_job', [
                'tenant_id' => $tenant['tenant_id'],
                'valid_chk' => 0,
                'title' => '注目情報',
                'disp_amount' => 4,
                'disp_type_ids' => '1,2,3',
                'text1' => 'main_copy',
                'text2' => 'corp_name_disp',
                'text3' => 'job_type_text',
                'text4' => 'station',
                'text1_length' => 30,
                'text2_length' => 30,
                'text3_length' => 30,
                'text4_length' => 30,
            ]);
        }
    }

    public function safeDown()
    {
        $this->truncateTable('hot_job');
    }
}
