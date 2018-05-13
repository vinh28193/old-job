<?php

use yii\db\Migration;

/**
 * Class m171101_004621_create_main_visual_tables
 */
class m171101_004621_create_main_visual_tables extends Migration
{
    /**
     * Up
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // メインビジュアル
        $this->createTable('main_visual', [
            'id' => $this->primaryKey()->unsigned()->notNull()->comment('ID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'area_id' => $this->integer()->comment('エリアID'),
            'type' => $this->string(16)->notNull()->comment('表示形式'),
            'valid_chk' => $this->boolean()->notNull()->defaultValue(true)->comment('有効フラグ'),
            'memo' => $this->text()->comment('管理用メモ'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('作成日時'),
            'updated_at' => $this->integer()->unsigned()->comment('更新日時'),
        ], $tableOptions);
        $this->addForeignKey(
            'fk-main_visual-area_id-area-id',
            'main_visual',
            'area_id',
            'area',
            'id'
        );

        // メインビジュアル画像
        $this->createTable('main_visual_image', [
            'id' => $this->primaryKey()->notNull()->comment('ID'),
            'main_visual_id' => $this->integer()->unsigned()->notNull()->comment('メインビジュアルID'),
            'file_name' => $this->string(256)->comment('ファイル名'),
            'file_name_sp' => $this->string(256)->comment('ファイル名'),
            'url' => $this->string(256)->comment('リンク先URL'),
            'url_sp' => $this->string(256)->comment('SP向けリンク先URL'),
            'content' => $this->string(64)->notNull()->comment('コンテンツ'),
            'sort' => $this->smallInteger()->unsigned()->notNull()->comment('並び順'),
            'valid_chk' => $this->boolean()->notNull()->defaultValue(true)->comment('有効フラグ'),
            'memo' => $this->text()->comment('管理用メモ'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('作成日時'),
            'updated_at' => $this->integer()->unsigned()->comment('更新日時'),
        ], $tableOptions);
        $this->addForeignKey(
            'fk-main_visual_image-main_visual_id-main_visual-id',
            'main_visual_image',
            'main_visual_id',
            'main_visual',
            'id'
        );
    }

    /**
     * Down
     */
    public function safeDown()
    {
        $this->dropTable('main_visual_image');
        $this->dropTable('main_visual');
    }
}
