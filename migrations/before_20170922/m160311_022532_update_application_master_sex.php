<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * 性別カラムの値を数値で持つように修正。
 * 男性 => 0
 * 女性 => 1
 */
class m160311_022532_update_application_master_sex extends Migration
{
    public function safeUp()
    {
        $this->update('application_master', ['sex' => 0], ['sex' => '男性']);
        $this->update('application_master', ['sex' => 1], ['sex' => '女性']);
        $this->alterColumn('application_master', 'sex',  'TINYINT COMMENT "性別"');
    }

    public function safeDown()
    {
        $this->alterColumn('application_master', 'sex', Schema::TYPE_STRING . ' COMMENT "性別"');
        $this->update('application_master', ['sex' => '男性'], ['sex' => 0]);
        $this->update('application_master', ['sex' => '女性'], ['sex' => 1]);
    }
    
}
