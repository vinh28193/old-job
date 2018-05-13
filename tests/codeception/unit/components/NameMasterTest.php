<?php
namespace app\components;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\NameMaster;
use yii\db\Query;

class NameMasterTest extends JmTestCase
{
    /**
     * getJobName()のtest
     */
    public function testGetJobName()
    {
        $model = NameMaster::findOne(['name_id' => '2']);
        verify($model->change_name)->equals(Yii::$app->nameMaster->JobName);
    }

    /**
     * getApplicationName()のtest
     */
    public function testGetApplicationName()
    {
        $model = NameMaster::findOne(['name_id' => '3']);
        verify($model->change_name)->equals(Yii::$app->nameMaster->ApplicationName);
    }
}
