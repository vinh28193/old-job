<?php

use app\models\manage\ManageMenuCategory;
use app\models\manage\ManageMenuMain;
use yii\db\Schema;
use yii\db\Migration;

class m151009_063441_add_icon_in_manage_menu_category extends Migration
{
    // アイコン設定(ソースにあったものだけなので後で最適なものに置き換えてください)
    private $categoryIcons = [
        1 => 'list-alt', // 求人現行
        2 => 'flag',// 代理店･掲載企業
//        3 => '', // コンテンツ
        4 => 'picture', // ギャラリー
        5 => 'align-left', // サイト分析
        6 => 'star', // 管理者
        7 => 'user', // 応募者
//        8 => '', // 登録者
        9 => 'envelope', // メール
        10 => 'pencil', // 項目管理
        11 => 'search', // 検索キー
//        12 => '', // 初期設定
    ];

    // アイコン設定(ソースにあったものだけなので後で最適なものに置き換えてください)
    private $mainIcons = [
        1 => 'th',
        79 => 'plus',
        3 => 'th',
        77 => 'plus',
        4 => 'th',
        75 => 'plus',
        69 => 'th',
        5 => 'th',
        70 => 'plus',
        71 => 'edit',
        6 => 'th',
    ];

    public function up()
    {
        // アイコンのカラム追加
        $this->addColumn(ManageMenuCategory::tableName(), 'icon_key', Schema::TYPE_STRING);
        $this->addColumn(ManageMenuMain::tableName(), 'icon_key', Schema::TYPE_STRING);

        foreach ($this->categoryIcons as $k => $v) {
            Yii::$app->db->createCommand()->update(ManageMenuCategory::tableName(),[
                'icon_key' => $v,
            ], [
                'manage_menu_category_id' => $k,
            ])->execute();
        }
        foreach ($this->mainIcons as $k => $v) {
            Yii::$app->db->createCommand()->update(ManageMenuMain::tableName(),[
                'icon_key' => $v,
            ], [
                'manage_menu_main_id' => $k,
            ])->execute();
        }
    }

    public function down()
    {
        $this->dropColumn(ManageMenuCategory::tableName(), 'icon_key');
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
