<?php


class SchemaTest extends \yii\codeception\DbTestCase
{
    use \Codeception\Specify;
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
    public function testGetQueryBuilder()
    {
        //$this->specify('test query builder class return', function(){
            $schema = new \proseeds\db\mysql\Schema();
            verify($schema->className())->equals(\proseeds\db\mysql\Schema::className());
            $builder = $schema->getQueryBuilder();
            verify($builder->className())->equals(\proseeds\db\mysql\QueryBuilder::className());
        //});
    }

}