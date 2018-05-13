<?php

use yii\db\Migration;

class m161220_230626_drop_corp_master_id_column_from_media_upload extends Migration
{
    public function up()
    {
        $this->dropColumn('media_upload','corp_master_id');
    }

    public function down()
    {
        $this->addColumn('media_upload', 'corp_master_id', $this->integer(11)->comment('corp_masterのカラムid'));
    }
}
