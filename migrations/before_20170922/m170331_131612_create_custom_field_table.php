<?php

use yii\db\Migration;

/**
 * Handles the creation of table 'custom_field'.
 * カスタムフィールドテーブルの作成
 */
class m170331_131612_create_custom_field_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('custom_field', [
            'id' => $this->primaryKey()->comment('主キー'),
            'tenant_id' => $this->integer(11)->comment('テナントID'),
            'custom_no' => $this->integer(11)->comment('カスタムNo'),
            'detail' => $this->text()->null()->comment('表示内容'),
            'url' => $this->string(2000)->null()->comment('URL'),
            'pict' => $this->string(255)->null()->comment('画像'),
            'valid_chk' => $this->boolean()->defaultValue(1)->comment('公開状況'),
            'created_at' => $this->integer(11)->comment('登録日時'),
            'updated_at' => $this->integer(11)->comment('更新日時')
        ], $tableOptions . ' COMMENT="カスタムフィールド"');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('custom_field');
    }
}
