<?php


class ManageMenuConfigsTest extends \yii\codeception\DbTestCase
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

    /**
     * __constructと__getのtest
     */
    public function testConstruct()
    {
        verify(Yii::$app->functionItemSet->admin->columnSetModel)->equals('app\models\manage\AdminColumnSet');
        verify(Yii::$app->functionItemSet->application->columnSetModel)->equals('app\models\manage\ApplicationColumnSet');
        verify(Yii::$app->functionItemSet->inquiry->columnSetModel)->equals('app\models\manage\InquiryColumnSet');
        verify(Yii::$app->functionItemSet->client->columnSetModel)->equals('app\models\manage\ClientColumnSet');
        verify(Yii::$app->functionItemSet->corp->columnSetModel)->equals('app\models\manage\CorpColumnSet');
        verify(Yii::$app->functionItemSet->member->columnSetModel)->equals('app\models\manage\MemberColumnSet');
        verify(Yii::$app->functionItemSet->job->columnSetModel)->equals('app\models\manage\JobColumnSet');
    }
}