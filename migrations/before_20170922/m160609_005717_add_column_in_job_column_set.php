<?php

use yii\db\Migration;

class m160609_005717_add_column_in_job_column_set extends Migration
{
    public function safeUp()
    {
        $this->addColumn('job_column_set', 'short_display', 'TINYINT(4) DEFAULT NULL COMMENT"簡易表示フラグ兼表示順"');
        $this->addColumn('job_column_set', 'search_result_display', 'TINYINT(4) DEFAULT NULL COMMENT"検索結果表示フラグ兼表示順"');
    }

    public function safeDown()
    {
        $this->dropColumn('job_column_set', 'short_display');
        $this->dropColumn('job_column_set', 'search_result_display');
    }
}