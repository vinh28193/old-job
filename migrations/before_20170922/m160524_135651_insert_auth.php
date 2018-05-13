<?php

use yii\db\Migration;

class m160524_135651_insert_auth extends Migration
{
    public function up()
    {
        /** @var \yii\rbac\DbManager $auth */
        // DbManagerを呼び出す
        $auth = Yii::$app->authManager;



        // "searchkey1Exception" という許可を追加
        $mediaUpLoadCreateException = $auth->createPermission('mediaUpLoadCreateException');
        $mediaUpLoadCreateException->description = '画像アップロード不許可';
        $auth->remove($mediaUpLoadCreateException);
        $auth->add($mediaUpLoadCreateException);
    }

    public function down()
    {
        //省略
    }
}
