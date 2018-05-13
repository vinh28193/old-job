<?php

use app\rbac\isOwnClientRule;
use yii\db\Migration;

class m160504_063716_make_own_client_auth extends Migration
{
    public function safeUp()
    {
        /** @var \yii\rbac\DbManager $auth */
        // DbManagerを呼び出す
        $auth = Yii::$app->authManager;
        // ルールを呼び出す
        // 自分自身のもののみ許可
        $isOwnClientRule = new isOwnClientRule();
        /** ルールを伴う許可を作成し、その許可を親の許可に関連付け、そのルールを伴った許可をロールに関連付ける */
        // ルールを追加する
        $auth->add($isOwnClientRule);
        $updateClient = $auth->createPermission('updateClient');
        $updateClient->description = '掲載企業更新許可';
        $auth->add($updateClient);
        // "isOwnClient" の呼び出し
        $isOwnClient = $auth->getPermission('isOwnClient');
        // "isOwnClient" は "updateClient" から使われる
        $auth->addChild($isOwnClient, $updateClient);

        $corpAdmin = $auth->getRole('corp_admin');
        $auth->addChild($corpAdmin, $isOwnClient);
        // isOwnClient許可の紐付け変更
        $this->update('auth_item', ['rule_name' => 'isOwnClient'], ['name' => 'isOwnClient']);
    }

    public function safeDown()
    {
    }
}
