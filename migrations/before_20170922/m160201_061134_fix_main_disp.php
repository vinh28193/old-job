<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160201_061134_fix_main_disp
 * main_dispを調整
 */
class m160201_061134_fix_main_disp extends Migration
{
    public function safeUp()
    {
        // システム値なのでVARCHARの桁数調整
        $this->alterColumn('main_disp', 'main_disp_name', 'VARCHAR(20) NOT NULL COMMENT "詳細メイン名"');
        // disp_type_cdのカラム名をテーブル名＋idという外部キーの命名規則に沿って変更
        $this->renameColumn('main_disp', 'disp_type_cd', 'disp_type_id');
        // item_columnのカラム名をcolumn_nameに変更し、桁数調整
        $this->renameColumn('main_disp', 'item_column', 'column_name');
        $this->alterColumn('main_disp', 'column_name', 'VARCHAR(30) NOT NULL COMMENT "job_masterのカラム"');
        // disp_chkの型を最適化
        $this->alterColumn('main_disp', 'disp_chk', 'BOOLEAN NOT NULL DEFAULT 1 COMMENT "表示チェック"');
    }

    public function safeDown()
    {
        $this->alterColumn('main_disp', 'disp_chk', 'TINYINT(4) NOT NULL DEFAULT 1 COMMENT "表示チェック"');
        $this->alterColumn('main_disp', 'column_name', 'VARCHAR(255) NOT NULL COMMENT "job_masterのカラム"');
        $this->renameColumn('main_disp', 'column_name', 'item_column');
        $this->renameColumn('main_disp', 'disp_type_id', 'disp_type_cd');
        $this->alterColumn('main_disp', 'main_disp_name', 'VARCHAR(255) NOT NULL DEFAULT 0 COMMENT "詳細メイン名"');
    }
}
