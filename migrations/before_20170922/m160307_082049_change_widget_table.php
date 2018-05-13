<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * ウィジェットテーブルの修正
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class m160307_082049_change_widget_table extends Migration
{
    public function safeUp()
    {
        //デフォルトウィジェット名
        $this->dropColumn('widget', 'widget_name_default');
        //ウィジェットタイプ
        $this->addColumn('widget', 'widget_type', 'TINYINT(1) DEFAULT 0 COMMENT \'ウィジェットタイプ(0=URL, 1=動画)\' AFTER widget_name');
        //ウィジェットレイアウトＩＤ
        $this->addColumn('widget', 'widget_layout_id', Schema::TYPE_SMALLINT . ' COMMENT \'ウィジェットレイアウトID\'');
        //ソート
        $this->addColumn('widget', 'sort', Schema::TYPE_SMALLINT . ' COMMENT \'同じレイアウトIDに登録した場合のウィジェットの表示順\'');
    }

    public function safeDown()
    {
        //デフォルトウィジェット名
        $this->addColumn('widget', 'widget_name_default', Schema::TYPE_STRING . ' COMMENT \'デフォルトウィジェット名\' AFTER widget_name');
        //ウィジェットタイプ
        $this->dropColumn('widget', 'widget_type');
        //ウィジェットレイアウトＩＤ
        $this->dropColumn('widget', 'widget_layout_id');
        //ソート
        $this->dropColumn('widget', 'sort');
    }
}
