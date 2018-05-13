<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Class m160120_025505_drop_tables
 * 以下のテーブルを削除する
 * migrate downできないので注意
 */
class m160120_025505_drop_tables extends Migration
{
    public $tables = [
        'admin_menu_exception',
        'affiliate',
        'application_affiliate',
        'area_cate',
        'astrology',
        'astrology_tmp',
        'auto_login_id',
        'client_charge_master',
        'client_option_no_disp',
        'enq_master',
        'enq_result_master',
        'enq_result_subset',
        'gateway',
        'job_affiliate',
        'job_limit',
        'job_review',
        'job_short_item_set_id',
        'job_station',
        'job_work',
        'link',
        'manage_menu_exception',
        'manage_menu_master',
        'member_merit',
        'mobile_id_access',
        'not_regist',
        'one_time_password',
        'pickup',
        'pref_route',
        'railroad_company_cd',
        'route_cd',
        'search_contents_master',
        'special',
        'special_url',
        'star_sign_master',
        'topics',
        'table 163',
        'table 172',
    ];

    public function safeUp()
    {
        echo 'テスト';
        foreach ($this->tables as $table) {
            $this->dropTable($table);
        }
    }

    public function safeDown()
    {
        echo 'このmigrationはdownできません';
    }
}
