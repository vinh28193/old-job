<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_020740_insert_default_tenant extends Migration
{
    public function up()
    {
        $this->insert('tenant', [
            'tenant_id' => 1,
            'tenant_code' => 'jm2',
            'tenant_name' => 'ProseedsJMTenant',
            'company_name' => 'Proseeds',
            'language_code' => 'ja',
            'regist_date' => '2015/10/27 00:00:00',
            'update_date' => '2015/10/27 00:00:00',
            'del_chk' => 0,
        ]);


    }

    public function down()
    {
        $this->delete('tenant', 'tenant_id=1');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
