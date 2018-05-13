<?php

use yii\db\Migration;

/**
 * Class m160317_023043_fix_birth_date_in_application_column_set
 * 応募者の誕生日のデータタイプををテキストに変更
 */
class m160317_023043_fix_birth_date_in_application_column_set extends Migration
{
    public function safeUp()
    {
        $this->update('application_column_set', ['data_type' => 'テキスト'], ['column_name' => 'birth_date']);
    }

    public function safeDown()
    {
        $this->update('application_column_set', ['data_type' => 'プルダウン'], ['column_name' => 'birth_date']);
    }
}
