<?php


class ConnectionTest extends \yii\codeception\DbTestCase
{
    use Codeception\Specify;
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
    public function testConnectionConstruct()
    {
        //$this->specify('test create instance', function(){
            $connection = new \proseeds\db\Connection();
            verify($connection->className())->equals(\proseeds\db\Connection::className());
            verify($connection->schemaMap['mysql'])->equals('proseeds\db\mysql\Schema');
        //});
    }

}