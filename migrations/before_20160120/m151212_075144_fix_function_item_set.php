<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m151212_075144_fix_function_item_set
 * corp_mailとagent_mail(function_item_idが70、71)のitem_columnを空文字にし、
 * item_columnのタイポ(誤jobTypSmall 正jobTypeSmall)を修正し、
 * item_columnがcorpMaster及びclientMasterのレコードのis_must_itemを0にする
 */
class m151212_075144_fix_function_item_set extends Migration
{
    public function safeUp()
    {
        // corp_mailとagent_mail(function_item_idが70、71)のitem_columnを空文字に
        $this->update('function_item_set', ['item_column' => ''], ['function_item_id' => [70, 71]]);

        // item_columnのタイポを修正
        $this->update('function_item_set', ['item_column' => 'jobTypeSmall'], ['item_column' => 'jobTypSmall']);

        // item_columnがcorpMaster及びclientMasterのレコードのis_must_itemを0に
        $this->update('function_item_set', ['is_must_item' => 0], ['item_column' => ['corpMaster', 'clientMaster']]);
    }

    public function safeDown()
    {
        // item_columnがcorpMaster及びclientMasterのレコードのis_must_itemを1に
        $this->update('function_item_set', ['is_must_item' => 1], ['item_column' => ['corpMaster', 'clientMaster']]);

        // タイポを元に戻すのは省略

        // corp_mailとagent_mail(function_item_idが70、71)のitem_columnを元に戻す
        $this->update('function_item_set', ['item_column' => 'corp_mail'], ['function_item_id' => 70]);
        $this->update('function_item_set', ['item_column' => 'agent_mail'], ['function_item_id' => 71]);
    }
}
