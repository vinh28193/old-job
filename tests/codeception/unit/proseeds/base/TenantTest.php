<?php


class TenantTest extends \yii\codeception\DbTestCase
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

    // tenant module test
    public function testGet()
    {
        //$this->specify('test get property', function(){
            $tenant = new \proseeds\base\Tenant();
            verify($tenant->id)->equals(1);
            verify($tenant->tenantCode)->equals('jm2');
            verify($tenant->primaryKey)->equals('tenant_id');
        //});
    }

}