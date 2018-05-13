<?php

use yii\db\Migration;

/*
 * Class m161013_122241_update_in_manage_menu_main_job_csv
 * 求人情報CSV一括登録・更新機能の権限を調整（運営元管理者のみにした）。
 */
class m161013_122241_update_in_manage_menu_main_job_csv extends Migration
{
    public function safeUp()
    {
        $this->update('manage_menu_main',
            [
                'permitted_role' => 'owner_admin',
            ],
            [
                'manage_menu_main_id' => '2',
                'manage_menu_category_id' => '1',
            ]
        );
    }

    public function safeDown()
    {
        $this->update('manage_menu_main',
            [
                'permitted_role' => 'client_admin',
            ],
            [
                'manage_menu_main_id' => '2',
                'manage_menu_category_id' => '1',
            ]
        );
    }
}
