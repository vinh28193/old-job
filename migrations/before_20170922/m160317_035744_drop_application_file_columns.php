<?php

use yii\db\Migration;

/**
 * Class m160317_035744_drop_application_file_columns
 * application_masterの不要なカラムを削除
 */
class m160317_035744_drop_application_file_columns extends Migration
{
    public function up()
    {
        $this->dropColumn('application_master', 'application_file');
        $this->dropColumn('application_master', 'application_file_disp');
    }

    public function down()
    {
        $this->addColumn('application_master', 'application_file', $this->string()->notNull() . ' COMMENT "添付ファイル名"');
        $this->addColumn('application_master', 'application_file_disp', $this->string()->notNull() . ' COMMENT "添付ファイル名(ユーザーが命名)"');
    }
}
