<?php
namespace tests\models\queries;

use app\models\FreeContentElement;
use app\models\queries\FreeContentElementQuery;
use tests\codeception\unit\JmTestCase;

/**
 * Class FreeContentElementQueryTest
 * @package tests\models\queries
 */
class FreeContentElementQueryTest extends JmTestCase
{
    /**
     * imageFileNamesのtest
     */
    public function testImageFileNames()
    {
        $imageFileNames = $this->query()->limit(20)->imageFileNames();
        verify($imageFileNames)->notEmpty();
        foreach ($imageFileNames as $fileName) {
            verify($fileName)->notEmpty();
            $this->tester->seeInDatabase(FreeContentElement::tableName(), ['image_file_name' => $fileName]);
        }
    }

    /**
     * FreeContentElementQueryを返す
     * @return FreeContentElementQuery
     */
    private function query():FreeContentElementQuery
    {
        return new FreeContentElementQuery(FreeContentElement::className());
    }
}
