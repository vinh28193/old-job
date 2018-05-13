<?php

use yii\db\Migration;

/**
 * 項目設定テーブル（[function_item_set]）のカラムを修正（コメントの内容を変更）
 */
class m151208_051529_function_item_set_column extends Migration
{
     public function up()
    {
        // 項目設定テーブルのカラムのコメントを修正（[COLUMN_TYPE]はクエリ実行のため必要なので残している。）
        $this->execute('ALTER TABLE function_item_set MODIFY item_data_type varchar(255) COMMENT "入力方法";');
        $this->execute('ALTER TABLE function_item_set MODIFY is_must_item tinyint(1) COMMENT "入力条件";');
    }

    public function down()
    {
        // 項目設定テーブルのカラムのコメントを元に戻している
        $this->execute('ALTER TABLE function_item_set MODIFY item_data_type varchar(255) COMMENT "入力項目形式";');
        $this->execute('ALTER TABLE function_item_set MODIFY is_must_item tinyint(1) COMMENT "必須入力";');
    }
}
