<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * ウィジェットレイアウトテーブル修正
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class m160307_082037_change_widget_layout_table extends Migration
{

    public function safeUp()
    {
        //エリアフラグ
        $this->renameColumn('widget_layout', 'area_type', 'area_flg');
        $this->alterColumn('widget_layout', 'area_flg', 'TINYINT(4) COMMENT \'全国、エリア判別(全国TOP：0、各エリアTOP共通レイアウト:1)\' AFTER tenant_id');
        //ウィジェットNo
        $this->dropColumn('widget_layout', 'widget_id');
        //ウィジェットレイアウトナンバー
        $this->addColumn('widget_layout', 'widget_layout_no', 'TINYINT(4) COMMENT \'ウィジェットレイアウトナンバー(1～6)\' AFTER area_flg');
        //ソート
        $this->dropColumn('widget_layout', 'sort');
    }

    public function safeDown()
    {
        //エリアフラグ
        $this->renameColumn('widget_layout', 'area_flg', 'area_type');
        //ウィジェットNo
        $this->addColumn('widget_layout', 'widget_id', 'INT(11)');
        //ウィジェットレイアウトナンバー
        $this->dropColumn('widget_layout', 'widget_layout_no');
        //ソート（コメント付けのみ）
        $this->addColumn('widget_layout', 'sort', Schema::TYPE_INTEGER . ' COMMENT \'\'');
    }

}
