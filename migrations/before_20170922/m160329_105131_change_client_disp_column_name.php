<?php

use yii\db\Migration;

/**
 * client_dispテーブルカラム名修正
 * client_column ⇒ column_name
 * disp_type_no ⇒　disp_type_id
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class m160329_105131_change_client_disp_column_name extends Migration
{
    public function safeUp()
    {
        //掲載企業カラム名
        $this->renameColumn('client_disp', 'client_column', 'column_name');
        //掲載タイプID
        $this->renameColumn('client_disp', 'disp_type_no', 'disp_type_id');
    }

    public function safeDown()
    {
        //掲載企業カラム名
        $this->renameColumn('client_disp', 'column_name', 'client_column');
        //掲載タイプID
        $this->renameColumn('client_disp', 'disp_type_id', 'disp_type_no');
    }
}
