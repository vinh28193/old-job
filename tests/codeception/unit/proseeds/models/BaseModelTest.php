<?php

namespace models;

use Codeception\Util\Debug;
use proseeds\models\BaseModel;
use tests\codeception\fixtures\TenantFixture;

class BaseModelTestModel extends BaseModel
{
    public static function tableName()
    {
        return 'tenant';
    }

    public function getFormatTable()
    {
        $array = parent::getFormatTable();
        return $array += [
           'del_chk' => [0 => '未削除', 1=> '削除済']
        ];
    }
}

class BaseModelTest extends \yii\codeception\DbTestCase
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
    public function testGetFormatTable()
    {
        //$this->specify("test table",function(){
            $model = new BaseModelTestModel();
            verify(is_array($model->formatTable))->true();
            verify($model->formatTable['del_chk'][0])->equals('未削除');
        //});
    }

    /**
     * test format as view
     */
    public function testFormatAsView()
    {
        //$this->specify("test format as view",function(){
            $model = BaseModelTestModel::findOne(['tenant_id' => 1]);
            $array = $model->formatAsView();
            verify(is_array($array))->true();
            verify($array['del_chk'][0])->notEmpty();
            verify($array['del_chk'][1])->notEmpty();
        //});
    }

    /**
     * test format as view
     */
    public function testFormatAsViewWithParam()
    {

    }
}