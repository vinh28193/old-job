<?php

use app\models\manage\SearchkeyMaster;
use yii\db\Migration;
use yii\helpers\ArrayHelper;

class m170202_080507_add_column_icon_flug_in_searchkey_master extends Migration
{
    const STATIC_ICON_FLG_KEY = [
        'pref',
        'station',
        'area',
        'pref_dist_master',
        'wage_category',
    ];

    public function safeUp()
    {
        if (ArrayHelper::isIn('icon_flg', SearchkeyMaster::getTableSchema()->columnNames)) {
            $this->dropColumn(SearchkeyMaster::tableName(), 'icon_flg');
        }
        $this->addColumn(SearchkeyMaster::tableName(), 'icon_flg', $this->boolean()->comment('アイコン表示フラグ'));
        $this->update('searchkey_master', ['icon_flg' => 0], ['not', ['table_name' => self::STATIC_ICON_FLG_KEY]]);
    }

    public function safeDown()
    {
        $this->dropColumn(SearchkeyMaster::tableName(), 'icon_flg');
    }
}
