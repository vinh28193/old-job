<?php
namespace models\queries;

use app\models\manage\AdminMaster;
use app\models\manage\CorpMaster;
use app\models\queries\AdminQuery;
use tests\codeception\unit\JmTestCase;

/**
 * AdminQueryのテスト
 */
class AdminQueryTest extends JmTestCase
{
    /**
     * AdminQueryをAdminMasterで使用したように取得
     * @return AdminQuery
     */
    private function getQuery()
    {
        return new AdminQuery(AdminMaster::className());
    }

    /**
     * addCorpAdminQueryのtest
     */
    public function testCorpAdminQuery()
    {
        /** @var AdminMaster[] $models */
        $corpMaster = CorpMaster::find()->where(['valid_chk' => 1])->one();
        $models = $this->getQuery()->addCorpAdminQuery($corpMaster->id)->all();
        verify($models)->notEmpty();
        $count = AdminMaster::find()->where(['valid_chk' => 1, 'corp_master_id' => $corpMaster->id, 'client_master_id' => null])->count();
        verify($models)->count((int)$count);
    }
}