<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `unnecessary_tables`.
 */
class m160611_092219_drop_unnecessary_tables extends Migration
{
    public function safeUp()
    {
        $this->dropTable('function_item_category');
        $this->dropTable('function_item_category_sort');
        $this->dropTable('function_item_id_sort');
        $this->dropTable('job_employment_type_tmp');
    }

    public function safeDown()
    {
        $this->createTable('function_item_category',[
            'function_item_id' => $this->integer(11)->notNull(),
            'function_category_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_function_item_category', 'function_item_category', ['function_item_id','function_category_id']);

        $this->createTable('function_item_category_sort',[
            'function_category_id' => $this->integer(11)->notNull()->defaultValue('0'),
            'category_name' => $this->text()->notNull(),
            'valid_chk' => $this->boolean()->notNull()->defaultValue('1'),
            'sort' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_function_category_id', 'function_item_category_sort', ['function_category_id']);

        $this->createTable('function_item_id_sort',[
            'function_item_id' => $this->integer(11)->notNull()->defaultValue('0'),
            'sort' => $this->integer(11)->notNull()->defaultValue('0'),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_function_item_id_sort', 'function_item_id_sort', ['function_item_id','sort']);

        $this->createTable('job_employment_type_tmp',[
            'employment_type_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_job_employment_type_tmp', 'job_employment_type_tmp', ['employment_type_cd','tmp_key']);
        $this->createIndex('idx_job_employment_type_tmp_1_tmp_key_2_tmp_id', 'job_employment_type_tmp', ['tmp_key','tmp_id']);
    }
}