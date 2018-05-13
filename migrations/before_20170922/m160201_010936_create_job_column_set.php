<?php

use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160201_010936_create_job_column_set
 * function_item_setのjob_master部分を分離・解体したテーブルを作る
 */
class m160201_010936_create_job_column_set extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('job_column_set', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'column_no' => 'TINYINT UNSIGNED NOT NULL COMMENT "メニューID"',
            'column_name' => 'VARCHAR(30) NOT NULL COMMENT "job_masterのカラム"',
            'label' => Schema::TYPE_STRING . ' NOT NULL COMMENT "項目名"',
            'data_type' => 'VARCHAR(10) NOT NULL COMMENT "入力方法"',
            'max_length' => Schema::TYPE_INTEGER . ' COMMENT "文字数上限"',
            'is_must' => Schema::TYPE_BOOLEAN . ' COMMENT "入力条件"',
            'is_in_list' => Schema::TYPE_BOOLEAN . ' COMMENT "検索一覧表示"',
            'is_in_search' => Schema::TYPE_BOOLEAN . ' COMMENT "検索項目表示"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' NOT NULL COMMENT "公開状況"',
            'freeword_search_flg' => Schema::TYPE_BOOLEAN . ' COMMENT "フリーワード検索フラグ"',
        ], $tableOptions . ' COMMENT="求人情報項目"');
        $this->createIndex('idx_job_column_set_1_tenant_id_2_column_name', 'job_column_set', ['tenant_id', 'column_name']);

        $this->moveFunctionItemSetRecords(1);
        $this->moveFunctionItemSetRecords(2);
    }

    public function safeDown()
    {
        $this->dropTable('job_column_set');
    }

    public function moveFunctionItemSetRecords($tenantId)
    {
        $data = (new Query)->select('*')->from('function_item_set')->where([
            'manage_menu_id' => 10,
            'tenant_id' => $tenantId,
        ])->andWhere(['not', [
            'function_item_id' => [61, 62, 63, 70, 71],
        ]])->andWhere(['not', [
            'is_option' => 2,
        ]])->all();

        for ($i = 1; $i <= 5; $i++) {
            $data[] = [
                'tenant_id' => $tenantId,
                'item_column' => 'job_pict_' . ($i - 1),
                'item_name' => '画像' . $i,
                'item_data_type' => '画像',
                'item_maxlength' => null,
                'is_must_item' => 0,
                'is_list_menu_item' => 0,
                'is_search_menu_item' => 0,
                'valid_chk' => 1,
                'freeword_flg' => 0,
            ];
        }
        for ($i = 3; $i <= 5; $i++) {
            $data[] = [
                'tenant_id' => $tenantId,
                'item_column' => 'job_pict_text_' . ($i - 1),
                'item_name' => '画像' . $i . 'テキスト',
                'item_data_type' => 'テキスト',
                'item_maxlength' => null,
                'is_must_item' => 0,
                'is_list_menu_item' => 0,
                'is_search_menu_item' => 0,
                'valid_chk' => 1,
                'freeword_flg' => 0,
            ];
        }

        foreach ($data as $index => $row) {
            $this->insert('job_column_set', [
                'id' => 50 * ($tenantId - 1) + $index + 1,
                'tenant_id' => $tenantId,
                'column_no' => $index + 1,
                'column_name' => $row['item_column'],
                'label' => $row['item_name'],
                'data_type' => $row['item_data_type'],
                'max_length' => $row['item_maxlength'],
                'is_must' => $row['is_must_item'],
                'is_in_list' => $row['is_list_menu_item'],
                'is_in_search' => $row['is_search_menu_item'],
                'valid_chk' => $row['valid_chk'],
                'freeword_search_flg' => $row['freeword_flg'],
            ]);
        }
    }
}
