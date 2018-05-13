<?php

use yii\db\Migration;

class m170831_002117_update_manage_menu_main extends Migration
{
    public function safeUp()
    {
        $this->update('manage_menu_main', ['title' => Yii::t('app', 'アクセス履歴')], 'href="/manage/secure/analysis-page/list"');
    }

    public function safeDown()
    {
        $this->update('manage_menu_main', ['title' => Yii::t('app', 'ページ別アクセス数確認')], 'href="/manage/secure/analysis-page/list"');
    }
}
