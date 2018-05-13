<?php

use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160201_093131_fix_option_sort_disp
 * option_sort_dispを調整し、テーブル名もlist_dispに
 */
class m160201_093131_fix_option_sort_disp extends Migration
{
    public function safeUp()
    {
        // テーブル名変更
        $this->renameTable('option_sort_disp', 'list_disp');
        // 不要カラム削除
        $this->dropColumn('list_disp', 'option_sort_disp_no');
        // 外部キーをcolumn_nameに
        $this->renameColumn('list_disp', 'function_item_set_id', 'column_name');
        $this->alterColumn('list_disp', 'column_name', 'VARCHAR(30) NOT NULL COMMENT "job_masterのカラム"');
        $data = (new Query)->select('*')->from('function_item_set')->where([
            'tenant_id' => 1,
        ])->all();
        foreach($data as $row){
            $this->update('list_disp', ['column_name' => $row['item_column']], ['column_name' => $row['function_item_id']]);
        }
        // disp_type_cdカラムを命名規則に沿ってリネーム
        $this->renameColumn('list_disp', 'disp_type_cd', 'disp_type_id');
    }

    public function safeDown()
    {
        $this->renameColumn('list_disp', 'disp_type_id', 'disp_type_cd');

        $data = (new Query)->select('*')->from('function_item_set')->where([
            'tenant_id' => 1,
        ])->all();
        foreach($data as $row){
            $this->update('list_disp', ['column_name' => $row['function_item_id']], ['column_name' => $row['item_column']]);
        }
        $this->alterColumn('list_disp', 'column_name', 'INT(11) NOT NULL COMMENT "テーブルfunction_item_setのカラムID"');
        $this->renameColumn('list_disp', 'column_name', 'function_item_set_id');

        $this->addColumn('list_disp' , 'option_sort_disp_no', 'INT(11) NOT NULL COMMENT "詳細順番-掲載タイプナンバー"');

        $this->renameTable('list_disp', 'option_sort_disp');
    }
}
