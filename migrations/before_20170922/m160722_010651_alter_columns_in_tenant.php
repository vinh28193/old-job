<?php

use yii\db\Migration;

class m160722_010651_alter_columns_in_tenant extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('tenant', 'tenant_code', $this->string(50)->notNull() . ' COMMENT"テナントコード(ドメイン名)"');
        $this->alterColumn('tenant', 'tenant_name', $this->string(255)->defaultValue(null) . ' COMMENT"テナント名(サイト名)"');
        $this->alterColumn('tenant', 'company_name', $this->string(255)->defaultValue(null) . ' COMMENT"会社名"');
    }

    public function safeDown()
    {
        $this->alterColumn('tenant', 'tenant_code', $this->string(20)->notNull() . ' COMMENT"テナントコード(ドメイン名)"');
        $this->alterColumn('tenant', 'tenant_name', $this->string(50)->defaultValue(null) . ' COMMENT"テナント名(サイト名)"');
        $this->alterColumn('tenant', 'company_name', $this->string(20)->defaultValue(null) . ' COMMENT"会社名"');
    }
}