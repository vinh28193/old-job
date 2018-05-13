<?php

use Codeception\Util\Debug;

class UploadTraitTestModel extends \yii\base\Model{
    use proseeds\models\UploadTrait;

    public $test;

    public function __construct()
    {
        $this->tempFilePath = __DIR__ . '/data/';
    }
}
//TODO PECLのAPDを入れて、override_functionを使えるようにしないとエラーになる。この機能はユニットテストでやらなくてもいいかも。
class UploadTraitTest extends \Codeception\TestCase\Test
{
    use \Codeception\Specify;
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $_FILES = array(
            'UploadTraitTestModel[test]' => array(
                'name' => 'test.png',
                'type' => 'image/png',
                'size' => 4070,
                'tmp_name' => __DIR__ . '/data/source-test.png',
                'error' => 0
            )
        );
    }

    protected function _after()
    {
        unset($_FILES);
    }

    /**
     * uploadFile処理のテスト
     */
    public function testUploadFile()
    {
        //$this->specify("test file upload",function(){
            $model = new UploadTraitTestModel();
            $filename = $model->uploadFile('test');
            verify("setted filename is same parameter", $filename)->equals($model->fileName);
            verify($model->test->name)->equals($_FILES['UploadTraitTestModel[test]']['name']);
            verify(file_exists($model->tempFilePath.$filename));
            unlink($model->tempFilePath.$filename);
        //});
    }

    /**
     * uploadFile処理のテスト(パス指定)
     */
    public function testUploadFileWithFolderPath()
    {
        //$this->specify("test file upload with folder path",function(){
            $model = new UploadTraitTestModel();
            $filename = $model->uploadFile('test', __DIR__."/data/dest/");
            verify("setted filename is same parameter", $filename)->equals($model->fileName);
            verify($model->test->name)->equals($_FILES['UploadTraitTestModel[test]']['name']);
            verify(file_exists(__DIR__."/data/dest/".$filename))->true();
            unlink(__DIR__."/data/dest/".$filename);
        //});
    }

    /**
     * uploadFile処理のテスト(存在しないパス指定)
     */
    public function testUploadFileWithNotExistsFolderPath()
    {
        //$this->specify("test file upload with not exists folder path",function(){
            $model = new UploadTraitTestModel();
            $filename = $model->uploadFile('test', __DIR__."/data/dummy/");
            verify("setted filename is same parameter", $filename)->false();
            verify(file_exists(__DIR__."/data/dest/".$filename))->false();
        //});
    }

    /**
     * moveFileのテスト
     */
    public function testMoveFile()
    {
        //$this->specify("test file move",function(){
            $model = new UploadTraitTestModel();
            $filepath = $model->moveFile('source-test.png', __DIR__ . '/data/', __DIR__ . '/data/dest/');
            verify($filepath)->notEmpty();
            verify(file_exists(__DIR__ . '/data/dest/source-test.png'))->true();
            rename(__DIR__ . '/data/dest/source-test.png', __DIR__ . '/data/source-test.png');
        //});
    }

    /**
     * moveFileのテスト(パス指定)
     */
    public function testMoveFileWithNotExistsFolderPath()
    {
        //$this->specify("with not exists folder path",function(){
            $model = new UploadTraitTestModel();
            $filepath = $model->moveFile('source-test.png', __DIR__ . '/data/dummy/', __DIR__ . '/data/dest/');
            verify($filepath)->isEmpty();
            verify(file_exists(__DIR__ . '/data/dest/source-test.png'))->false();
        //});
    }
}
/*
override_function('is_uploaded_file', '$filename', 'return file_exists($filename);');
function is_uploaded_file($filename)
{
    //Check only if file exists
    return file_exists($filename);
}
*/
//override_function('move_uploaded_file', '$filename,$destination', 'return copy($filename, $destination);');
/*
function move_uploaded_file($filename, $destination)
{
    //Copy file
    return copy($filename, $destination);
}
*/
