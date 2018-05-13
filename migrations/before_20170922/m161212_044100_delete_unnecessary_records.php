<?php

use yii\db\Migration;
use yii\db\Query;

class m161212_044100_delete_unnecessary_records extends Migration
{
    private $deleteMenus = [
        '/manage/secure/member/list',
        '/manage/secure/mail_auto/mail_auto_regist.jsp',
        '/manage/secure/widget/list',
        '/manage/secure/option-member/list',
        '/manage/secure/pref/list',
        '/manage/secure/dist/list',
        '/manage/secure/route/list',
        '/manage/secure/station/list',
        '/manage/secure/manager/manager_regist.jsp',
        '/manage/secure/managemenu/managemenu_regist.jsp',
        '/manage/secure/index/index_regist.jsp',
        '/manage/secure/mail/mail_list.jsp',
        '/manage/secure/mail_client/mail_client_list.jsp',
        '/manage/secure/mail_log/mail_log_list.jsp',
        '/manage/secure/analysis_ranking/analysis_ranking_list.jsp',
        '/manage/secure/application_mail/application_mail_list.jsp',
        '/manage/secure/option_scout/list',
        '/manage/secure/scout_mail/scout_list.jsp',
        '/manage/secure/scout_mail_log/scout_log_list.jsp',
        '/manage/secure/mail_set/scout_set_regist.jsp',
        '/manage/secure/client_charge/client_charge_list.jsp',
        '/manage/secure/charge_manage/charge_search.jsp',
        '/manage/secure/search/list',
        '/manage/secure/member/update',
    ];

    private $deleteMenuCates = [
        'サイト分析',
        '登録者',
        'メール'
    ];

    public function safeUp()
    {
        $this->delete('application_column_set', ['column_name' => '']);
        $this->delete('manage_menu_main', ['href' => $this->deleteMenus]);
        $this->delete('manage_menu_category', ['title' => $this->deleteMenuCates]);
    }

    public function safeDown()
    {
        $tenants = (new Query)->select('tenant_id')->from('tenant')->all();
        foreach ($tenants as $tenant) {
            $this->insert('application_column_set', [
                'tenant_id' => $tenant['tenant_id'],
                'column_name' => '',
                'label' => '閲覧状況',
                'data_type' => 'ラジオボタン',
                'max_length' => null,
                'is_must' => 1,
                'is_in_list' => 1,
                'is_in_search' => 1,
                'valid_chk' => 0,
                'is_sync' => 0,
                'sync_target' => null,
            ]);
        }

        foreach ($this->deleteMenus as $deleteMenus) {
            foreach ($tenants as $tenant) {
                $this->insert('manage_menu_main', [
                    'tenant_id' => $tenant['tenant_id'],
                    'manage_menu_category_id' => 1,
                    'title' => 'test',
                    'href' => $deleteMenus,
                    'valid_chk' => 1,
                    'sort' => 1,
                    'icon_key' => 'list',
                    'permitted_role' => 'owner_admin',
                    'exception' => '',
                ]);
            }
        }

        foreach ($this->deleteMenuCates as $deleteMenuCates) {
            foreach ($tenants as $tenant) {
                $this->insert('manage_menu_category', [
                    'tenant_id' => $tenant['tenant_id'],
                    'title' => $deleteMenuCates,
                    'sort' => 1,
                    'icon_key' => 'list-alt',
                    'valid_chk' => 1,
                    'manage_menu_category_no' => 1,
                ]);
            }
        }
    }
}
