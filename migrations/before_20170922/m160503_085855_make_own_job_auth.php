<?php

use app\rbac\isOwnJobRule;
use yii\db\Migration;

class m160503_085855_make_own_job_auth extends Migration
{

    public function safeUp()
    {
        /** @var \yii\rbac\DbManager $auth */
        // DbManagerを呼び出す
        $auth = Yii::$app->authManager;
        // ルールを呼び出す
        // 自分自身のもののみ許可
        $isOwnJobRule = new isOwnJobRule();
        /** ルールを伴う許可を作成し、その許可を親の許可に関連付け、そのルールを伴った許可をロールに関連付ける */
        // ルールを追加する
        $auth->add($isOwnJobRule);
        // "updateJob" という許可を追加
        $updateJob = $auth->createPermission('updateJob');
        $updateJob->description = '求人原稿更新許可';
        $auth->add($updateJob);
        // "isOwnJob" という許可を作成し、それに'isOwnJob'ルールを関連付ける。
        $isOwnJob = $auth->createPermission('isOwnJob');
        $isOwnJob->description = 'それが自分自身の求人原稿であるか';
        $isOwnJob->ruleName = 'isOwnJob';
        $auth->add($isOwnJob);

        // "isOwnJob" は "updateJob" から使われる
        $auth->addChild($isOwnJob, $updateJob);

        // "client"と"corp" に自分の掲載企業詳細を閲覧することを許可する
        $clientAdmin = $auth->getRole('client_admin');
        $corpAdmin = $auth->getRole('corp_admin');
        $auth->addChild($clientAdmin, $isOwnJob);
        $auth->addChild($corpAdmin, $isOwnJob);
    }

    public function safeDown()
    {
        // すみません、一旦省略させていただきます。
    }
}
