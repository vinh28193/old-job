<?php

use yii\db\Migration;

class m170809_090102_add_column_into_site_html_table extends Migration
{

    /** 変更対象のテーブル名 */
    const TABLE = 'site_html';

    /** 追加するカラム */
    const COLUMNS = [
        ['name' => 'another_html', 'comment' => 'その他解析タグHTML',],
    ];

    public function safeUp()
    {
        foreach (self::COLUMNS AS $column) {
            $this->addColumn(self::TABLE, $column['name'], $this->text()->comment($column['comment']));
        }
    }

    public function safeDown()
    {
        foreach (self::COLUMNS AS $column) {
            $this->dropColumn(self::TABLE, $column['name']);
        }
    }
}
