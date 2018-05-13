<?php

use yii\db\Migration;

/**
 * 'dist'テーブルに置いて、全テナントで共通化するにあたり、'tenant_id'、'valid_chk'、'sort'
 * のカラムを削除する。
 */
class m160711_041149_drop_column_in_dist extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('dist', 'tenant_id');
        $this->dropColumn('dist', 'valid_chk');
        $this->dropColumn('dist', 'sort');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->addColumn('dist', 'tenant_id', $this->integer(11)->notNull() . ' COMMENT "テナントID"');
        $this->addColumn('dist', 'valid_chk', 'TINYINT(1) DEFAULT 1 COMMENT "状態"');
        $this->addColumn('dist', 'sort', $this->integer(11)->defaultValue(0). ' COMMENT "表示順"');
    }
}
