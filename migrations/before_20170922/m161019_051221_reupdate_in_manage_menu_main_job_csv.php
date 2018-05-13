<?php

use yii\db\Migration;

/*
 * Class m161019_051221_reupdate_in_manage_menu_main_job_csv
 * 求人情報CSV一括登録・更新機能がtenant_idが2以上のテナントに
 * 反映されなかったので、修正。
 */
class m161019_051221_reupdate_in_manage_menu_main_job_csv extends Migration
{
    public function safeUp()
    {
        $this->update('manage_menu_main', [
            'title' => '求人情報CSV一括登録・更新',
            'href' => '/manage/secure/job-csv/index',
            'valid_chk' => 1,
            'permitted_role' => 'owner_admin',
            'exception' => 'jobCsvException',
        ], ['href' => '/manage/secure/job_csv/job_csv_index.jsp']);
    }

    public function safeDown()
    {
        $this->update('manage_menu_main', [
            'title' => 'CSVファイルで一括登録する',
            'href' => '/manage/secure/job_csv/job_csv_index.jsp',
            'valid_chk' => 0,
            'permitted_role' => 'owner_admin',
        ], [
            'AND',
            ['href' => '/manage/secure/job-csv/index'],
            //tenant_id = 1のものに関しては、すでに実装出来ていたのでdownはtenant_1が2以上のもののみにしております。
            ['>=', 'tenant_id', 2]
        ]);
    }
}
