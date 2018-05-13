<?php

use yii\db\Schema;
use yii\db\Migration;

class m151203_023006_update_manage_menu_main extends Migration
{
    public function up()
    {
        $this->update("manage_menu_main", ["valid_chk" => "0"], "href = '/manage/secure/job/update'");
        $this->update("manage_menu_main", ["valid_chk" => "0"], "href = '/manage/secure/application/update'");
        $this->update("manage_menu_main", ["valid_chk" => "0"], "href = '/manage/secure/member/update'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-search/search1/list"], "href = '/manage/secure/option_search/option_search_1/'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-search/search2/list"], "href = '/manage/secure/option_search/option_search_2/'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-search/search3/list"], "href = '/manage/secure/option_search/option_search_3/'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/search/list"], "href = '/manage/secure/option_search/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/media-upload/list"], "href = '/manage/secure/media_upload/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/media-upload/create"], "href = '/manage/secure/media_upload/create'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-job/list"], "href = '/manage/secure/option_job/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-corp/list"], "href = '/manage/secure/option_corp/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-client/list"], "href = '/manage/secure/option_client/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-admin/list"], "href = '/manage/secure/option_admin/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-application/list"], "href = '/manage/secure/option_application/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-member/list"], "href = '/manage/secure/option_member/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option-disptype/list"], "href = '/manage/secure/option_disptype/list'");

    }

    public function down()
    {
        $this->update("manage_menu_main", ["valid_chk" => "1"], "href = '/manage/secure/job/update'");
        $this->update("manage_menu_main", ["valid_chk" => "1"], "href = '/manage/secure/application/update'");
        $this->update("manage_menu_main", ["valid_chk" => "1"], "href = '/manage/secure/member/update'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_search/option_search_1/"], "href = '/manage/secure/option-search/search1/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_search/option_search_2/"], "href = '/manage/secure/option-search/search2/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_search/option_search_3/"], "href = '/manage/secure/option-search/search3/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/media_upload/list"], "href = '/manage/secure/media-upload/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/media_upload/create"], "href = '/manage/secure/media-upload/create'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_job/list"], "href = '/manage/secure/option-job/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_corp/list"], "href = '/manage/secure/option-corp/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_client/list"], "href = '/manage/secure/option-client/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_admin/list"], "href = '/manage/secure/option-admin/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_application/list"], "href = '/manage/secure/option-application/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_member/list"], "href = '/manage/secure/option-member/list'");
        $this->update("manage_menu_main", ["href" => "/manage/secure/option_disptype/list"], "href = '/manage/secure/option-disptype/list'");

        echo "m151203_023006_update_manage_menu_main cannot be reverted.\n";
        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
