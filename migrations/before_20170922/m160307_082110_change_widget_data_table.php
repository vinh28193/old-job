<?php

use yii\db\Migration;
use yii\db\Schema;

/**
 * ウィジェットデータテーブルの修正
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class m160307_082110_change_widget_data_table extends Migration
{

    public function safeUp()
    {
        $this->dropTable('content_job');
        $this->dropTable('content_url');
        $this->dropTable('content_movie');
        //ウィジェットデータテーブル
        $this->renameTable('content_master', 'widget_data');
        //コンテンツNo
        $this->dropColumn('widget_data', 'content_no');
        //エリア
        $this->addColumn('widget_data', 'area_id', 'TINYINT(4) COMMENT \'エリア 全国はnull\' AFTER widget_id');
        //コンテンツ名
        $this->dropColumn('widget_data', 'content_name');
        //コンテンツ種類
        $this->dropColumn('widget_data', 'content_type');
        //動画タグ
        $this->addColumn('widget_data', 'movie_tag', Schema::TYPE_TEXT . ' COMMENT \'動画タグ youtube埋め込みiframeタグ\' AFTER description');
        //URL
        $this->addColumn('widget_data', 'url', Schema::TYPE_STRING . ' COMMENT \'URL\' AFTER movie_tag');
    }

    public function safeDown()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        //コンテンツ原稿マッチング
        $this->createTable('content_job', [
            'id' => Schema::TYPE_PK . ' COMMENT \'ID\'',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'テナントID\'',
            'content_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'テーブルcontent_masterのカラムid\'',
            'job_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'テーブルjob_masterのカラムid\'',
            'zenkoku_flg' => 'TINYINT(1) NOT NULL COMMENT \'全国フラグ(0=全国TOPに表示させない, 1=全国TOPに表示させる)\'',
                ], $tableOptions . ' COMMENT=\'コンテンツ_原稿マッチング\''
        );
        //コンテンツURLマッチング
        $this->createTable('content_url', [
            'id' => Schema::TYPE_PK . ' COMMENT \'ID\'',
            'tenant_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'テナントID\'',
            'content_master_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'テーブルcontent_masterのカラムid\'',
            'area_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'テーブルarea_cdのカラムid(全国TOPは0とする)\'',
            'url' => Schema::TYPE_TEXT . ' NOT NULL COMMENT \'URL\'',
                ], $tableOptions . ' COMMENT=\'コンテンツ_URLマッチング\''
        );
        //コンテンツ動画タグマッチング
        $this->createTable('content_movie', [
            'id' => Schema::TYPE_PK . ' COMMENT \'ID\'',
            'tenant_id' => Schema::TYPE_INTEGER . '  NOT NULL COMMENT \'テナントID\'',
            'content_master_id' => Schema::TYPE_SMALLINT . ' NOT NULL COMMENT \'テーブルcontent_masterのカラムid\'',
            'area_cd_id' => Schema::TYPE_INTEGER . ' NOT NULL COMMENT \'テーブルarea_cdのカラムid(全国TOPは0とする)\'',
            'movie_tag' => Schema::TYPE_TEXT . ' NOT NULL COMMENT \'動画タグ\'',
                ], $tableOptions . ' COMMENT=\'コンテンツ_動画タグマッチング\''
        );

        //ウィジェットデータテーブル
        $this->renameTable('widget_data', 'content_master');
        //コンテンツNo
        $this->addColumn('content_master', 'content_no', Schema::TYPE_INTEGER . ' COMMENT \'コンテンツナンバー\' AFTER tenant_id');
        //エリア
        $this->dropColumn('content_master', 'area_id');
        //コンテンツ名
        $this->addColumn('content_master', 'content_name', Schema::TYPE_STRING . ' COMMENT \'コンテンツ名\' AFTER content_no');
        //コンテンツ種類
        $this->addColumn('content_master', 'content_type', 'TINYINT(4) COMMENT \'コンテンツ名\' AFTER content_no');
        //動画タグ
        $this->dropColumn('content_master', 'movie_tag');
        //URL
        $this->dropColumn('content_master', 'url');
    }

}
