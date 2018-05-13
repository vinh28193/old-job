<?php

use yii\db\Migration;

/**
 * Handles the creation of table `hot_job`.
 */
class m170712_084558_create_hot_job_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB';
        }
        $this->createTable('hot_job', [
            'id' => $this->primaryKey()->comment('主キーID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'valid_chk' => $this->boolean()->notNull()->defaultValue(1)->comment('公開状況'),
            'title' => $this->string(40)->notNull()->defaultValue('')->comment('タイトル'),
            'disp_amount' => $this->integer(11)->notNull()->defaultValue(4)->comment('表示する求人原稿数'),
            'disp_type_ids' => $this->string(30)->notNull()->defaultValue('1,2,3')->comment('表示する掲載タイプ'),
            'text1' => $this->string(30)->notNull()->defaultValue('')->comment('テキスト1に求人原稿の何を表示するか'),
            'text2' => $this->string(30)->notNull()->defaultValue('')->comment('テキスト2に求人原稿の何を表示するか'),
            'text3' => $this->string(30)->notNull()->defaultValue('')->comment('テキスト3に求人原稿の何を表示するか'),
            'text4' => $this->string(30)->notNull()->defaultValue('')->comment('テキスト4に求人原稿の何を表示するか'),
            'text1_length' => $this->integer(11)->notNull()->defaultValue(30)->comment('テキスト1の文字数制限'),
            'text2_length' => $this->integer(11)->notNull()->defaultValue(30)->comment('テキスト2の文字数制限'),
            'text3_length' => $this->integer(11)->notNull()->defaultValue(30)->comment('テキスト3の文字数制限'),
            'text4_length' => $this->integer(11)->notNull()->defaultValue(30)->comment('テキスト4の文字数制限'),
        ], $tableOptions);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('hot_job');
    }
}