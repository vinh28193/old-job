<?php

use yii\db\Migration;

/*
 * Class m160916_100658_insert_manage_menu_main_of_job_csv
 * 求人情報CSV一括登録・更新機能をメニューに出す。
 */
class m160916_100658_insert_manage_menu_main_of_job_csv extends Migration
{
    public function safeUp()
    {
        $this->update('manage_menu_main',
            [
                'title' => '求人情報CSV一括登録・更新',
                'href' => '/manage/secure/job-csv/index',
                'valid_chk' => 1,
                'permitted_role' => 'client_admin',
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
                'title' => 'CSVファイルで一括登録する',
                'href' => '/manage/secure/job_csv/job_csv_index.jsp',
                'valid_chk' => 0,
                'permitted_role' => 'owner_admin',
            ],
            [
                'manage_menu_main_id' => '2',
                'manage_menu_category_id' => '1',
            ]
        );
    }
}
