<?php

use app\rbac\isOwnApplicationRule;
use yii\db\Migration;

class m160504_060558_make_own_application_auth extends Migration
{
    public function safeUp()
    {
        /** @var \yii\rbac\DbManager $auth */
        // DbManagerを呼び出す
        $auth = Yii::$app->authManager;
        // ルールを呼び出す
        // 自分自身のもののみ許可
        $isOwnApplicationRule = new isOwnApplicationRule();
        /** ルールを伴う許可を作成し、その許可を親の許可に関連付け、そのルールを伴った許可をロールに関連付ける */
        // ルールを追加する
        $auth->add($isOwnApplicationRule);
        // "updateApplication" という許可を追加
        $updateApplication = $auth->createPermission('updateApplication');
        $updateApplication->description = '応募者更新許可';
        $auth->add($updateApplication);
        // "isOwnApplication" という許可を作成し、それに'isOwnApplication'ルールを関連付ける。
        $isOwnApplication = $auth->createPermission('isOwnApplication');
        $isOwnApplication->description = 'それが自分自身の応募者であるか';
        $isOwnApplication->ruleName = 'isOwnApplication';
        $auth->add($isOwnApplication);

        // "isOwnApplication" は "updateApplication" から使われる
        $auth->addChild($isOwnApplication, $updateApplication);

        // "client"と"corp" に自分の掲載企業詳細を閲覧することを許可する
        $clientAdmin = $auth->getRole('client_admin');
        $corpAdmin = $auth->getRole('corp_admin');
        $auth->addChild($clientAdmin, $isOwnApplication);
        $auth->addChild($corpAdmin, $isOwnApplication);
    }

    public function safeDown()
    {
        // すみません、一旦省略させていただきます。
    }
}
