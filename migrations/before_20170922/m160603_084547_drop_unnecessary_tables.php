<?php

use yii\db\Migration;

class m160603_084547_drop_unnecessary_tables extends Migration
{
    public function safeUp()
    {
        $this->dropTable('merit_cd');
        $this->dropTable('job_merit');
        $this->dropTable('job_merit_tmp');
        $this->dropTable('option_search_cd');
        $this->dropTable('option_search_category_cd');
        $this->execute('DROP TABLE IF EXISTS sample_pict');
        $this->dropTable('manage_menu_admin_exception');
        $this->dropTable('employment_type_cd');
        $this->dropTable('function_item_set');
        $this->dropTable('function_item_subset');
        $this->dropTable('job_employment_type');
        $this->dropTable('job_option_search');
    }

    public function safeDown()
    {
        $this->createTable('merit_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'merit_cd' => $this->integer(11)->notNull(). ' COMMENT "メリットコード"',
            'merit_name' => $this->integer(11)->notNull(). ' COMMENT "メリット名"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->integer(11)->notNull(). ' COMMENT "表示順"',
            'merit_category_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルmerit_category_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="メリット"');
        $this->addPrimaryKey('pk_merit_cd', 'merit_cd', ['id']);
        $this->createIndex('idx_merit_cd_1_tenant_id', 'merit_cd', ['tenant_id']);

        $this->createTable('job_merit',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルjob_masterのカラムid"',
            'merit_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルmerit_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-メリット関連"');
        $this->addPrimaryKey('pk_job_merit', 'job_merit', ['id','tenant_id']);
        
        $this->createTable('job_merit_tmp',[
            'merit_cd' => $this->integer(11)->notNull(),
            'tmp_key' => $this->integer(11)->notNull(),
            'tmp_id' => $this->integer(11)->notNull(),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB ');
        $this->addPrimaryKey('pk_job_merit_tmp', 'job_merit_tmp', ['merit_cd','tmp_key','tmp_id']);
        $this->createIndex('idx_job_merit_tmp_1_tmp_key_2_tmp_id', 'job_merit_tmp', ['tmp_key','tmp_id']);

        $this->createTable('option_search_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'option_search_cd' => $this->integer(11)->notNull(). ' COMMENT "オプション検索キーコード"',
            'option_search_category_cd_id' => $this->integer(11)->defaultValue(null). ' COMMENT "テーブルoption_search_category_cdカラムid"',
            'option_search_name' => $this->string(255)->notNull(). ' COMMENT "オプション検索キー名"',
            'sort_no' => $this->integer(11)->notNull(). ' COMMENT "表示順"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="オプション検索キー"');
        $this->addPrimaryKey('pk_option_search_cd', 'option_search_cd', ['id']);
        $this->createIndex('idx_option_search_cd_1_tenant_id', 'option_search_cd', ['tenant_id']);

        $this->createTable('option_search_category_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'option_search_category_cd' => $this->integer(11)->notNull(). ' COMMENT "オプション検索キーカテゴリコード"',
            'option_display_type' => $this->integer(11)->defaultValue(0). ' COMMENT "表示タイプ(0=チェックボックス 1=ラジオボタン)"',
            'option_search_type' => $this->integer(11)->defaultValue(0). ' COMMENT "検索タイプ(0=AND検索 1=OR検索)"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="オプション検索キーカテゴリ"');
        $this->addPrimaryKey('pk_option_search_category_cd', 'option_search_category_cd', ['id']);
        $this->createIndex('idx_option_search_category_cd_1_tenant_id', 'option_search_category_cd', ['tenant_id']);

        $this->createTable('sample_pict',[
            'sample_pict_id' => $this->bigInteger(20)->notNull(),
            'file_name' => $this->string(200)->defaultValue(null),
            'updated_at' => $this->integer(11)->notNull(). ' COMMENT "更新日時"',
            'admin_id' => $this->integer(11)->defaultValue(null),
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="サンプルピクチャ"');
        $this->addPrimaryKey('pk_sample_pict_id', 'sample_pict', ['sample_pict_id']);

        $this->createTable('manage_menu_admin_exception',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'admin_master_id' => $this->integer(11)->defaultValue(null),
            'manage_menu_main_id' => $this->integer(11)->notNull(). ' COMMENT "管理画面小メニューID"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="管理者除外メニューセット"');
        $this->addPrimaryKey('pk_manage_menu_admin_exception', 'manage_menu_admin_exception', ['id']);
        $this->createIndex('idx_manage_menu_admin_exception', 'manage_menu_admin_exception', ['tenant_id','admin_master_id','manage_menu_main_id']);


        $this->createTable('employment_type_cd',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'employment_type_name' => $this->string(255)->notNull(). ' COMMENT "雇用形態名"',
            'valid_chk' => $this->boolean()->defaultValue(1). ' COMMENT "状態"',
            'sort' => $this->integer(11)->notNull(). ' COMMENT "表示順"',
            'employment_type_cd' => $this->integer(11)->notNull(). ' COMMENT "雇用形態コード"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="雇用形態コード"');
        $this->addPrimaryKey('pk_employment_type_cd', 'employment_type_cd', ['id']);
        $this->createIndex('idx_employment_type_cd_1_tenant_id', 'employment_type_cd', ['tenant_id']);

        $this->createTable('function_item_set',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'function_item_id' => $this->integer(11)->notNull(). ' COMMENT "項目管理ID"',
            'manage_menu_id' => $this->integer(11)->defaultValue(null). ' COMMENT "メニューID"',
            'item_name' => $this->string(255)->defaultValue(null). ' COMMENT "項目名"',
            'item_data_type' => $this->string(255)->defaultValue(null). ' COMMENT "入力方法"',
            'item_maxlength' => $this->integer(11)->defaultValue(null). ' COMMENT "文字数上限"',
            'is_must_item' => $this->boolean()->defaultValue(null). ' COMMENT "入力条件"',
            'is_list_menu_item' => $this->boolean()->defaultValue(0). ' COMMENT "検索一覧表示"',
            'is_search_menu_item' => $this->boolean()->defaultValue(0). ' COMMENT "検索項目表示"',
            'is_system_item' => $this->boolean()->defaultValue(0). ' COMMENT "システム項目"',
            'valid_chk' => $this->boolean()->defaultValue(0). ' COMMENT "公開状況"',
            'index_no' => $this->integer(11)->defaultValue(null). ' COMMENT "行番号"',
            'item_default_name' => $this->string(255)->defaultValue(null). ' COMMENT "デフォルト項目名"',
            'is_option' => $this->boolean()->defaultValue(0). ' COMMENT "オプション検索キー名"',
            'is_file' => $this->boolean()->defaultValue(0). ' COMMENT "ファイルアップロード項目"',
            'place_holder' => $this->text(). ' COMMENT "プレースホルダ"',
            'item_column' => $this->string(200)->defaultValue(null). ' COMMENT "カラム名"',
            'freeword_flg' => $this->boolean()->defaultValue(0). ' COMMENT "フリーワード検索フラグ"',
            'is_common' => $this->boolean()->defaultValue(0). ' COMMENT "連携項目"',
            'is_common_target_id' => $this->integer(11)->defaultValue(null). ' COMMENT "連携項目ID"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="項目管理"');
        $this->addPrimaryKey('pk_function_item_set', 'function_item_set', ['id']);
        $this->createIndex('idx_function_item_set_1_tenant_id_2_function_item_id', 'function_item_set', ['tenant_id','function_item_id']);

        $this->createTable('function_item_subset',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'function_item_subset_id' => $this->integer(11)->notNull(). ' COMMENT "項目管理サブセットID"',
            'function_item_set_id' => $this->integer(11)->notNull(). ' COMMENT "項目管理ID"',
            'function_item_subset_name' => $this->string(255)->notNull(). ' COMMENT "選択肢名"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="項目管理サブセット"');
        $this->addPrimaryKey('pk_function_item_subset', 'function_item_subset', ['id']);
        $this->createIndex('idx_function_item_subset_1_tenant_id_2_function_item_id', 'function_item_subset', ['tenant_id','function_item_subset_id']);

        $this->createTable('job_employment_type',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->defaultValue(0). ' COMMENT "テーブルjob_masterのカラムid"',
            'employment_type_cd_id' => $this->integer(11)->defaultValue(0). ' COMMENT "テーブルemployment_type_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-雇用形態関連"');
        $this->addPrimaryKey('pk_job_employment_type', 'job_employment_type', ['id','tenant_id']);

        $this->createTable('job_option_search',[
            'id' => $this->integer(11)->notNull(). ' COMMENT "ID"',
            'tenant_id' => $this->integer(11)->notNull(). ' COMMENT "テナントID"',
            'job_master_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルjob_masterのカラムid"',
            'employment_type_cd_id' => $this->integer(11)->notNull(). ' COMMENT "テーブルoption_search_cdのカラムid"',
        ],  'CHARACTER SET utf8 COLLATE utf8_bin ENGINE=InnoDB COMMENT="仕事-オプション検索キー関連"');
        $this->addPrimaryKey('pk_job_option_search', 'job_option_search', ['id','tenant_id']);
    }
}