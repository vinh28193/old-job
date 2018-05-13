<?php

use yii\db\Migration;

class m170525_022424_add_columns_into_site_html extends Migration
{

    /** 変更対象のテーブル名 */
    const TABLE = 'site_html';

    /** 追加するカラム */
    const COLUMNS = [
        ['name' => 'analytics_html', 'comment' => 'アナリティクスタグHTML',],
        ['name' => 'conversion_html', 'comment' => 'コンバージョンタグHTML',],
        ['name' => 'remarketing_html', 'comment' => 'リマーケティングタグHTML',],
    ];

    public function up()
    {
        foreach (self::COLUMNS AS $column) {
            $this->addColumn(self::TABLE, $column['name'], $this->text()->comment($column['comment']));
        }
    }

    public function down()
    {
        foreach (self::COLUMNS AS $column) {
            $this->dropColumn(self::TABLE, $column['name']);
        }
    }
}
