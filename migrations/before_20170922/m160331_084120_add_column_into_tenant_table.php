<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * テナントテーブルへ求人詳細画面のURL名カラム追加
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class m160331_084120_add_column_into_tenant_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tenant', 'kyujin_detail_dir', Schema::TYPE_STRING . ' COMMENT\'求人詳細ディレクトリ名\' AFTER tenant_code');
        //初期値
        $this->update('tenant', ['kyujin_detail_dir' => 'kyujin']);
    }

    public function safeDown()
    {
        $this->dropColumn('tenant', 'kyujin_detail_dir');
    }
}
