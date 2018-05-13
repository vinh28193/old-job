<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160310_064547_fix_is_must_of_application_status
 * application_column_setの状況を任意固定に
 */
class m160310_064547_fix_is_must_of_application_status extends Migration
{

    public function safeUp()
    {
        $this->update('application_column_set', ['is_must' => null], ['column_name' => 'status']);
    }

    public function safeDown()
    {
    }
}
