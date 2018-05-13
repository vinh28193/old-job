<?php

use yii\db\Schema;
use yii\db\Migration;

class m160201_070220_fix_disp_type_cd extends Migration
{
    public function safeUp()
    {
        $this->renameTable('disp_type_cd', 'disp_type');
        $this->renameColumn('disp_type', 'disp_type_cd', 'disp_type_no');
        $this->dropColumn('disp_type', 'sort_no');
        $this->dropColumn('disp_type', 'del_chk');
    }

    public function safeDown()
    {
        $this->addColumn('disp_type', 'del_chk', 'BOOLEAN DEFAULT 0 COMMENT "削除フラグ"');
        $this->addColumn('disp_type', 'sort_no', 'TINYINT(4) COMMENT "表示順"');
        $this->renameColumn('disp_type', 'disp_type_no', 'disp_type_cd');
        $this->renameTable('disp_type', 'disp_type_cd');
    }
}
