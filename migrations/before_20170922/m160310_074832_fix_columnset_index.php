<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160310_074832_fix_columnset_index
 * 各column_setテーブルのインデックス修正
 */
class m160310_074832_fix_columnset_index extends Migration
{
    const CREATE_INFO = [
        'job' => '求人原稿',
        'admin' => '管理者',
        'application' => '応募者',
        'client' => '掲載企業',
        'corp' => '代理店',
    ];

    public function safeUp()
    {
        foreach (self::CREATE_INFO as $key => $name) {
            $this->dropIndex('idx_' . $key . '_column_set_1_tenant_id_2_column_name', '' . $key . '_column_set');
            $this->createIndex('idx_' . $key . '_column_set_1_tenant_id_2_column_name_3_column_no', '' . $key . '_column_set', ['tenant_id', 'column_name', 'column_no']);
        }
    }

    public function safeDown()
    {
        foreach (self::CREATE_INFO as $key => $name) {
            $this->dropIndex('idx_' . $key . '_column_set_1_tenant_id_2_column_name_3_column_no', '' . $key . '_column_set');
            $this->createIndex('idx_' . $key . '_column_set_1_tenant_id_2_column_name', '' . $key . '_column_set', ['tenant_id', 'column_name']);
        }
    }
}
