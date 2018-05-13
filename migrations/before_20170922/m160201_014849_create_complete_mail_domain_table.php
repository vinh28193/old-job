<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * オートコンプリート候補テーブルの作成マイグレーション
 * @author Yukinori Nakamura <y_nakamura@id-frontier.jp>
 */
class m160201_014849_create_complete_mail_domain_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        // オートコンプリートテーブル
        $this->createTable('complete_mail_domain', [
            'id' => Schema::TYPE_PK . ' COMMENT "ID"',
            'mail_domain' => Schema::TYPE_STRING . ' NOT NULL COMMENT "オートコンプリートするメールドメイン"',
            'valid_chk' => Schema::TYPE_BOOLEAN . ' NOT NULL DEFAULT 0 COMMENT "状態"',
        ], $tableOptions. ' COMMENT="メールドメインオートコンプリート"');
    }

    public function down()
    {
        // オートコンプリートテーブル
        $this->dropTable('complete_mail_domain');
    }
}
