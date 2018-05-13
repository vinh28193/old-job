<?php

use yii\db\Migration;

class m160617_054007_drop_study_cd extends Migration
{
    public function safeUp()
    {
        $this->execute('DROP TABLE IF EXISTS study_cd');
    }
    
    public function safeDown()
    {
        $this->createTable('study_cd',[
            'study_cd' => $this->integer(11)->notNull(),
            'study_name' => $this->text()->notNull(),
            'valid_chk' => $this->boolean()->notNull()->defaultValue('1'),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_study_cd', 'study_cd', ['study_cd']);
    }
}
