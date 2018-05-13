<?php

use yii\db\Query;
use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160127_081041_move_oiwai_in_job_master
 * お祝い金のテーブルをjob_masterに統合
 * レコードも整合性を保ったまま引き継げます
 */

class m160127_081041_move_oiwai_in_job_master extends Migration
{
    public function safeUp()
    {
        // データ抽出
        $data = (new Query)->select('*')->from('job_oiwai_price')->all();
        // カラム追加
        $this->addColumn('job_master', 'oiwai_price', Schema::TYPE_INTEGER . ' NOT NULL COMMENT "お祝い金額"');
        // レコード挿入
        foreach ($data as $row) {
            $this->update('job_master', ['oiwai_price' => $row['oiwai_price']], ['id' => $row['job_master_id']]);
        }
        // テーブル削除
        $this->dropTable('job_oiwai_price');
    }

    public function safeDown()
    {
        // tableOptionの代入
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        // レコード関連データ抽出
        $data = (new Query)->select(['oiwai_price', 'tenant_id', 'id'])->from('job_master')->all();
        // テーブル作成
        $this->createTable('job_oiwai_price', [
            'id' => Schema::TYPE_INTEGER . ' COMMENT "ID"',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テナントID"',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "テーブルjob_masterのカラムid"',
            'oiwai_price' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT "お祝い金額"',
        ], $tableOptions . ' COMMENT="仕事-お祝い金関連"');

        $this->addPrimaryKey('pk_job_oiwai_price', 'job_oiwai_price', ['id', 'tenant_id']);
        $this->alterColumn('job_oiwai_price', 'id', Schema::TYPE_INTEGER . ' NOT NULL AUTO_INCREMENT COMMENT "ID"');
        $this->execute('ALTER TABLE job_oiwai_price PARTITION BY HASH (tenant_id) PARTITIONS 2000;');

        foreach ($data as $index => $row) {
            $this->insert('job_oiwai_price', [
                'id' => $index + 1,
                'tenant_id' => $row['tenant_id'],
                'job_master_id' => $row['id'],
                'oiwai_price' => $row['oiwai_price']
            ]);
        }
        // カラム削除
        $this->dropColumn('job_master', 'oiwai_price');
    }
}
