<?php

use yii\db\Migration;

/**
 * Handles the creation of table `header_footer`.
 */
class m170303_061403_create_header_footer_table extends Migration
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

        //検索キーマスター
        $this->createTable('header_footer', [
            'id' => $this->primaryKey()->comment('主キーID'),
            'tenant_id' => $this->integer(11)->notNull()->comment('テナントID'),
            'logo_file_name' => $this->string(30)->notNull()->comment('ロゴ画像'),
            'tel_no' => $this->string(30)->notNull()->comment('電話番号'),

            'header_text1' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク1テキスト'),
            'header_text2' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク2テキスト'),
            'header_text3' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク3テキスト'),
            'header_text4' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク4テキスト'),
            'header_text5' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク5テキスト'),
            'header_text6' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク6テキスト'),
            'header_text7' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク7テキスト'),
            'header_text8' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク8テキスト'),
            'header_text9' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク9テキスト'),
            'header_text10' => $this->string(20)->defaultValue('')->comment('ヘッダーリンク10テキスト'),

            'header_url1' => $this->text()->comment('ヘッダーリンク1URL'),
            'header_url2' => $this->text()->comment('ヘッダーリンク2URL'),
            'header_url3' => $this->text()->comment('ヘッダーリンク3URL'),
            'header_url4' => $this->text()->comment('ヘッダーリンク4URL'),
            'header_url5' => $this->text()->comment('ヘッダーリンク5URL'),
            'header_url6' => $this->text()->comment('ヘッダーリンク6URL'),
            'header_url7' => $this->text()->comment('ヘッダーリンク7URL'),
            'header_url8' => $this->text()->comment('ヘッダーリンク8URL'),
            'header_url9' => $this->text()->comment('ヘッダーリンク9URL'),
            'header_url10' => $this->text()->comment('ヘッダーリンク10URL'),

            'footer_text1' => $this->string(20)->defaultValue('')->comment('フッターリンク1テキスト'),
            'footer_text2' => $this->string(20)->defaultValue('')->comment('フッターリンク2テキスト'),
            'footer_text3' => $this->string(20)->defaultValue('')->comment('フッターリンク3テキスト'),
            'footer_text4' => $this->string(20)->defaultValue('')->comment('フッターリンク4テキスト'),
            'footer_text5' => $this->string(20)->defaultValue('')->comment('フッターリンク5テキスト'),
            'footer_text6' => $this->string(20)->defaultValue('')->comment('フッターリンク6テキスト'),
            'footer_text7' => $this->string(20)->defaultValue('')->comment('フッターリンク7テキスト'),
            'footer_text8' => $this->string(20)->defaultValue('')->comment('フッターリンク8テキスト'),
            'footer_text9' => $this->string(20)->defaultValue('')->comment('フッターリンク9テキスト'),
            'footer_text10' => $this->string(20)->defaultValue('')->comment('フッターリンク10テキスト'),

            'footer_url1' => $this->text()->comment('フッターリンク1URL'),
            'footer_url2' => $this->text()->comment('フッターリンク2URL'),
            'footer_url3' => $this->text()->comment('フッターリンク3URL'),
            'footer_url4' => $this->text()->comment('フッターリンク4URL'),
            'footer_url5' => $this->text()->comment('フッターリンク5URL'),
            'footer_url6' => $this->text()->comment('フッターリンク6URL'),
            'footer_url7' => $this->text()->comment('フッターリンク7URL'),
            'footer_url8' => $this->text()->comment('フッターリンク8URL'),
            'footer_url9' => $this->text()->comment('フッターリンク9URL'),
            'footer_url10' => $this->text()->comment('フッターリンク10URL'),

            'copyright' => $this->string(200)->defaultValue('')->comment('コピーライト'),
        ], $tableOptions);
        $this->createIndex('idx_header_footer_tenant_id', 'header_footer', 'tenant_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('header_footer');
    }
}
