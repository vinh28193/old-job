<?php


class QueryBuilderTest extends \yii\codeception\DbTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testBuild()
    {
        $query = \app\models\manage\CorpMaster::find();
        $builder = new \proseeds\db\mysql\QueryBuilder(Yii::$app->db);
        list($sql, $params) = $builder->build($query);
        verify($sql)->contains('tenant_id');
    }

    // tests
    public function testInsert()
    {
        $builder = new \proseeds\db\mysql\QueryBuilder(Yii::$app->db);
        $params = ['10','dummy'];
        $result = $builder->insert('corp_master',['corp_no','corp_name'], $params);
        verify($result)->contains('tenant_id');
    }

}