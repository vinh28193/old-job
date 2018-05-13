<?php

use app\models\manage\ManageMenuMain;
use yii\db\Schema;
use yii\db\Migration;

class m151017_102846_re_add_icon_key extends Migration
{
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
        $this->addColumn(ManageMenuMain::tableName(), 'icon_key', Schema::TYPE_STRING);

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
        $this->dropColumn(ManageMenuMain::tableName(), 'icon_key');
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
