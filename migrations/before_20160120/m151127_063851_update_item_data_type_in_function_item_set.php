<?php

use app\models\manage\FunctionItemSet;
use yii\db\Schema;
use yii\db\Migration;

/**
 * created by Noboru Sakamoto
 * Class m151127_063851_update_item_data_type_in_function_item_set
 * item_columnが****_noであるレコードのitem_data_typeを数字に変える
 */
class m151127_063851_update_item_data_type_in_function_item_set extends Migration
{
    public function safeUp()
    {
        $this->update(FunctionItemSet::tableName(),
            ['item_data_type' => '数字'],
            [
                'and',
                ['like', 'item_column',['_no']],
                ['not', ['item_column' => 'tel_no']]
            ]);
    }

    public function safeDown()
    {
        $this->update(FunctionItemSet::tableName(),
            ['item_data_type' => 'テキスト'],
            [
                'and',
                ['like', ['item_column' => '_no']],
                ['not', ['item_column' => 'tel_no']]
            ]);
    }
}
