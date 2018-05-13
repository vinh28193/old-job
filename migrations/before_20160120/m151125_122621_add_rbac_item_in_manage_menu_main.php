<?php

use app\models\manage\ManageMenuMain;
use yii\db\Schema;
use yii\db\Migration;

/**
 * created by Noboru Sakamoto
 * Class m151125_122621_add_rbac_item_in_manage_menu_main
 * 権限機能改修に合わせてpermitted_roleカラムとexceptionカラム、およびその値を追加する
 * exceptionに関してはまだ全てはできていないため随時追加していく
 */
class m151125_122621_add_rbac_item_in_manage_menu_main extends Migration
{
    public $rbacExceptions = [
        1 => 'jobListException',//求人一覧
        79 => 'jobCreateException',//求人登録

        3 => 'corpListException',//代理店一覧
        77 => 'corpCreateException',//代理店登録

        4 => 'clientListException',//掲載企業一覧
        78 => 'clientCreateException',//掲載企業登録

        69 => 'mediaUpLoadListException',//画像の確認
        75 => 'mediaUpLoadCreateException',//画像のアップロード

        5 => 'adminListException',//管理者一覧
        70 => 'adminCreateException',//管理者登録
        71 => 'profileException',//プロフ

        6 => 'applicationListException',//応募者一覧
        //項目管理
        22 => 'optionJobException',//求人原稿
        23 => 'optionCorpException',//代理店
        24 => 'optionClientException',//掲載企業
        25 => 'optionAdminException',//管理者
        26 => 'optionApplicationException',//応募者
        27 => 'optionMemberException',//登録者
        51 => 'optionDisptypeException',//掲載タイプ
        //検索キー
        76 => 'searchException',//項目設定
        28 => 'jobTypeException',//職種
        29 => 'areaException',//エリア
        32 => 'prefdistException',//地域グループ
        35 => 'employmentException',//雇用形態
        36 => 'occupationMemberException',//属性
        37 => 'worktimeException',//勤務時間
        38 => 'wageException',//給与
        52 => 'meritException',//メリット
        72 => 'option1Exception',//オプション1
        73 => 'option2Exception',//オプション2
        74 => 'option3Exception',//オプション3
    ];

    public function safeUp()
    {
        // カラムの追加
        $this->addColumn(ManageMenuMain::tableName(), 'permitted_role', Schema::TYPE_STRING);
        $this->addColumn(ManageMenuMain::tableName(), 'exception', Schema::TYPE_STRING . ' not null');

        // ロール名書き込み
        $this->update(ManageMenuMain::tableName(), ['permitted_role' => 'client_admin'], ['client_available' => 1]);
        $this->update(ManageMenuMain::tableName(), ['permitted_role' => 'corp_admin'], ['corp_available' => 1, 'client_available' => 0]);
        $this->update(ManageMenuMain::tableName(), ['permitted_role' => 'owner_admin'], ['corp_available' => 0, 'client_available' => 0]);

        // Exception許可書き込み
        foreach ($this->rbacExceptions as $k => $v) {
            Yii::$app->db->createCommand()->update(ManageMenuMain::tableName(), [
                'exception' => $v,
            ], [
                'manage_menu_main_id' => $k,
            ])->execute();
        }
    }

    public function safeDown()
    {
        $this->dropColumn(ManageMenuMain::tableName(), 'permitted_role');
        $this->dropColumn(ManageMenuMain::tableName(), 'exception');
    }

}
