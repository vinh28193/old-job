<?php

use app\models\manage\ManageMenuCategory;
use yii\db\Migration;
use yii\db\Schema;

class m160826_061611_add_column_in_manage_menu_category extends Migration
{
    // manage_menu_category_noのデフォルト値
    private $numberMap = [
        1 => '求人原稿',
        2 => '代理店',
        3 => '掲載企業',
        4 => 'ウィジェット',
        5 => 'ギャラリー',
        6 => 'サイト分析',
        7 => '管理者',
        8 => '応募者',
        9 => '登録者',
        10 => 'メール',
        11 => '項目管理',
        12 => '検索キー',
        13 => '初期設定',
    ];


    public function up()
    {
        $colname = 'manage_menu_category_no';
        $table = ManageMenuCategory::tableName();
        $this->addColumn($table, $colname, $this->integer(11)->notNull()->defaultValue(0) . ' COMMENT "カテゴリNo"');

        $map = array_flip($this->numberMap);
        foreach ($map as $title => $no) {
            $this->update($table, [$colname => $no], ['title' => $title]);
        }
    }

    public function down()
    {
        $this->dropColumn(ManageMenuCategory::tableName(), 'manage_menu_category_no');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
