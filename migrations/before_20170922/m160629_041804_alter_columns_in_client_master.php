<?php

use yii\db\Migration;

class m160629_041804_alter_columns_in_client_master extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('client_master', 'tel_no', $this->string(30) . ' COMMENT"電話番号"');
        $this->alterColumn('client_master', 'address', $this->string(255) . ' COMMENT"住所"');
        $this->alterColumn('client_master', 'client_business_outline', $this->string(255) . ' COMMENT"事業内容"');
        $this->alterColumn('client_master', 'client_corporate_url', $this->string(2000) . ' COMMENT"ホームページ"');
        $this->alterColumn('client_master', 'admin_memo', $this->string(255) . ' COMMENT"運営元メモ"');
    }

    public function safeDown()
    {
        $this->alterColumn('client_master', 'tel_no', $this->string(30)->notNull() . ' COMMENT"電話番号"');
        $this->alterColumn('client_master', 'address', $this->text()->notNull() . ' COMMENT"住所"');
        $this->alterColumn('client_master', 'client_business_outline', $this->text()->notNull() . ' COMMENT"事業内容"');
        $this->alterColumn('client_master', 'client_corporate_url', $this->string(255) . ' COMMENT"ホームページ"');
        $this->alterColumn('client_master', 'admin_memo', $this->text() . ' COMMENT"運営元メモ"');
    }
}
