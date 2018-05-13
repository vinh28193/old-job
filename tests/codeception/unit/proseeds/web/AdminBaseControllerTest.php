<?php

use Codeception\Util\Debug;

class TestAdminBaseController extends \proseeds\web\AdminBaseController
{

}

class AdminBaseControllerTest extends \Codeception\TestCase\Test
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
    public function testActionIndex()
    {
        //TODO:Controllerはユニットテストいらないかも。あとwebというフォルダ名はもうcontrollersでいいね。
        /*
        $this->specify("test of action index", function(){
            $controller = new TestAdminBaseController('testadminbase', 'default');
            Debug::debug($controller);
            $response = $controller->actionIndex();
            Debug::debug($response);
            verify($response->statusCode)->equals(302);
            verify($response->headers->get('Location'))->equals("list");
        });
        */
    }

}