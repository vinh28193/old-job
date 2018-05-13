<?php

use yii\db\Migration;

class m160522_102052_insert_auth extends Migration
{
    public function up()
    {
        /** @var \yii\rbac\DbManager $auth */
        // DbManagerを呼び出す
        $auth = Yii::$app->authManager;

        // "searchkey1Exception" という許可を追加
        $searchkey1Exception = $auth->createPermission('searchkey1Exception');
        $searchkey1Exception->description = '検索キー1不許可';
        $auth->add($searchkey1Exception);

        // "searchkey2Exception" という許可を追加
        $searchkey2Exception = $auth->createPermission('searchkey2Exception');
        $searchkey2Exception->description = '検索キー2不許可';
        $auth->add($searchkey2Exception);

        // "searchkey3Exception" という許可を追加
        $searchkey3Exception = $auth->createPermission('searchkey3Exception');
        $searchkey3Exception->description = '検索キー3不許可';
        $auth->add($searchkey3Exception);

        // "searchkey4Exception" という許可を追加
        $searchkey4Exception = $auth->createPermission('searchkey4Exception');
        $searchkey4Exception->description = '検索キー4不許可';
        $auth->add($searchkey4Exception);

        // "searchkey5Exception" という許可を追加
        $searchkey5Exception = $auth->createPermission('searchkey5Exception');
        $searchkey5Exception->description = '検索キー5不許可';
        $auth->add($searchkey5Exception);

        // "searchkey6Exception" という許可を追加
        $searchkey6Exception = $auth->createPermission('searchkey6Exception');
        $searchkey6Exception->description = '検索キー6不許可';
        $auth->add($searchkey6Exception);

        // "searchkey7Exception" という許可を追加
        $searchkey7Exception = $auth->createPermission('searchkey7Exception');
        $searchkey7Exception->description = '検索キー7不許可';
        $auth->add($searchkey7Exception);

        // "searchkey8Exception" という許可を追加
        $searchkey8Exception = $auth->createPermission('searchkey8Exception');
        $searchkey8Exception->description = '検索キー8不許可';
        $auth->add($searchkey8Exception);

        // "searchkey9Exception" という許可を追加
        $searchkey9Exception = $auth->createPermission('searchkey9Exception');
        $searchkey9Exception->description = '検索キー9不許可';
        $auth->add($searchkey9Exception);

        // "searchkey10Exception" という許可を追加
        $searchkey10Exception = $auth->createPermission('searchkey10Exception');
        $searchkey10Exception->description = '検索キー10不許可';
        $auth->add($searchkey10Exception);

        // "searchkey11Exception" という許可を追加
        $searchkey11Exception = $auth->createPermission('searchkey11Exception');
        $searchkey11Exception->description = '検索キー11不許可';
        $auth->add($searchkey11Exception);

        // "searchkey12Exception" という許可を追加
        $searchkey12Exception = $auth->createPermission('searchkey12Exception');
        $searchkey12Exception->description = '検索キー12不許可';
        $auth->add($searchkey12Exception);

        // "searchkey13Exception" という許可を追加
        $searchkey13Exception = $auth->createPermission('searchkey13Exception');
        $searchkey13Exception->description = '検索キー13不許可';
        $auth->add($searchkey13Exception);

        // "searchkey14Exception" という許可を追加
        $searchkey14Exception = $auth->createPermission('searchkey14Exception');
        $searchkey14Exception->description = '検索キー14不許可';
        $auth->add($searchkey14Exception);

        // "searchkey15Exception" という許可を追加
        $searchkey15Exception = $auth->createPermission('searchkey15Exception');
        $searchkey15Exception->description = '検索キー15不許可';
        $auth->add($searchkey15Exception);

        // "searchkey16Exception" という許可を追加
        $searchkey16Exception = $auth->createPermission('searchkey16Exception');
        $searchkey16Exception->description = '検索キー16不許可';
        $auth->add($searchkey16Exception);

        // "searchkey17Exception" という許可を追加
        $searchkey17Exception = $auth->createPermission('searchkey17Exception');
        $searchkey17Exception->description = '検索キー17不許可';
        $auth->add($searchkey17Exception);

        // "searchkey18Exception" という許可を追加
        $searchkey18Exception = $auth->createPermission('searchkey18Exception');
        $searchkey18Exception->description = '検索キー18不許可';
        $auth->add($searchkey18Exception);

        // "searchkey19Exception" という許可を追加
        $searchkey19Exception = $auth->createPermission('searchkey19Exception');
        $searchkey19Exception->description = '検索キー19不許可';
        $auth->add($searchkey19Exception);

        // "searchkey20Exception" という許可を追加
        $searchkey20Exception = $auth->createPermission('searchkey20Exception');
        $searchkey20Exception->description = '検索キー20不許可';
        $auth->add($searchkey20Exception);

    }

    public function down()
    {
        //省略
    }

}
