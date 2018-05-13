<?php

namespace models;

use tests\codeception\fixtures\TenantFixture;
use proseeds\models\Tenant;

class TenantTest extends \yii\codeception\DbTestCase
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

    /**
     * @return array
     */
    public function fixtures()
    {
        return [
            'tenant' => TenantFixture::className(),
        ];
    }

    /**
     * test tabel name
     */
    public function testTableName()
    {
        //$this->specify("test table name",function(){
            $name = Tenant::tableName();
            verify($name)->equals('tenant');
        //});
    }

    /**
     * test find
     */
    public function testFindAll()
    {
        //$this->specify("test find",function(){
            $model = Tenant::find()->all();
            verify(count($model))->equals(9);
        //});
    }

    /**
     * test find one
     */
    public function testFindOne()
    {
        //$this->specify("test find one",function(){
            $model = Tenant::findOne(['tenant_id' => 1]);
            verify($model->tenant_id)->equals(1);
            verify($model->tenant_code)->equals("jm2");
            verify($model->tenant_name)->equals("Bednar, Hudson and Wilkinson");
        //});
    }

    public function testRules()
    {
        $model = new \proseeds\models\Tenant();
        verify($model->rules())->notEmpty();
    }

    public function testAttributeLabels()
    {
        $model = new \proseeds\models\Tenant();
        verify($model->attributeLabels())->notEmpty();
    }
}