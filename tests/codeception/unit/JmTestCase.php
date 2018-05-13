<?php
namespace tests\codeception\unit;

use app\common\Helper\JmUtils;
use app\modules\manage\models\Manager;
use Codeception\Specify;
use proseeds\base\console\Tenant;
use ReflectionClass;
use tests\codeception\JmFixture;
use Yii;
use yii\base\Object as YiiObj;
use yii\codeception\DbTestCase;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\UploadedFile;

/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/06/21
 * Time: 15:05
 */
class JmTestCase extends DbTestCase
{
    const UNIT_FIXTURE_BASE = 'tests\codeception\unit\fixtures\\';
    const COMMON_FIXTURE_BASE = 'tests\codeception\fixtures\\';

    const COMMON_FIXTURES = [
        // auth
        'AuthRule',
        'AuthItem',
        // column settings
        'AdminColumnSet',
        'ApplicationColumnSet',
        'ClientColumnSet',
        'CorpColumnSet',
        'InquiryColumnSet',
        'InquiryColumnSubset',
        'JobColumnSet',
        // search keys
        'SearchkeyMaster',
        'Area',
        'Pref',
        'Dist',
        'Station',
        // job display
        'DispType',
        'MainDisp',
        'ListDisp',
        'ClientDisp',
        // menus
        'ManageMenuMain',
        'ManageMenuCategory',
        // other settings
        'Policy',
        'HotJob',
        'HotJobPriority',
    ];

    use Specify;

    /**
     * 特に代入等しなくても勝手に入ります
     * @var \UnitTester
     */
    protected $tester;

    /**
     * テストメソッドの最初にファイルデータのstatic propertyを削除
     */
    public function setUp()
    {
        parent::setUp();
        UploadedFile::reset();
    }

    /**
     * deepCloneからshallowCloneにスイッチする
     */
    protected function _before()
    {
        $this->specifyConfig()->shallowClone();
    }

    /**
     * roleを元にidentityをセットする
     * @param string $role
     */
    protected function setIdentity($role)
    {
//        $adminFixture = static::getFixtureInstance('admin_master');
//        $adminFixture->load();
        $adminRecords = require(__DIR__ . '/fixtures/data/admin_master.php');
        Yii::$app->user->identity = Manager::findIdentity($adminRecords[$role . Yii::$app->tenant->id]['id']);
    }

    /**
     * fixtureからidを元にレコードを抽出する(hasOne)
     * @param JmFixture $fixture
     * @param integer $id
     * @return array
     */
    protected function findRecordById(JmFixture $fixture, $id)
    {
        return ArrayHelper::getValue(ArrayHelper::index($fixture->data(), 'id'), $id);
    }

    /**
     * fixtureからForeignKeyを元にレコードを抽出する(hasMany)
     * @param JmFixture $fixture
     * @param $keyName
     * @param $value
     * @return mixed
     */
    protected function findRecordsByForeignKey(JmFixture $fixture, $keyName, $value)
    {
        return ArrayHelper::getValue(ArrayHelper::index($fixture->data(), null, $keyName), $value, []);
    }

    /**
     * IDEの補完を生かすため作成
     * @return null|Manager
     */
    protected function getIdentity()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::$app->user->identity;
    }

    /**
     * auth_assignmentとadmin_masterのデータ不整合が無い必要があります
     * @param $authName
     * @return null|Manager
     */
    protected function getManager($authName)
    {
        $adminIds = (new Query())->select('user_id')->from('auth_assignment')->where(['item_name' => $authName])->column();
        return Manager::findOne(['id' => $adminIds]);
    }

    /**
     * idにテナントに応じた数を足す
     * @param $number
     * @param $tableName
     * @return mixed
     */
    protected function id($number, $tableName)
    {
        $fixture = static::getFixtureName($tableName);
        return $number + $fixture::RECORDS_PER_TENANT * (Yii::$app->tenant->id - 1);
    }

    /**
     * fixtureのfullNameを返す
     * @param $tableName
     * @return string
     */
    protected static function getFixtureName($tableName)
    {
        $className = Inflector::camelize($tableName);
        if (in_array($className, self::COMMON_FIXTURES)) {
            return self::COMMON_FIXTURE_BASE . $className . 'Fixture';
        } else {
            return self::UNIT_FIXTURE_BASE . $className . 'Fixture';
        }
    }

    /**
     * @param $tableName
     * @return JmFixture
     */
    protected static function getFixtureInstance($tableName)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(static::getFixtureName($tableName));
    }

    /**
     * テナントを返す
     * IDEの補完を生かすため作成
     * @return Tenant
     */
    protected function tenant()
    {
        return Yii::$app->tenant;
    }

    /**
     * ファイルがpostされた状態を再現する
     * @param string $formName
     * @param string $attribute
     */
    protected function loadFilePost(string $formName, string $attribute)
    {
        UploadedFile::reset();
        if (!JmUtils::isEmpty($formName) && !JmUtils::isEmpty($attribute)) {
            $_FILES = [
                $formName => [
                    'name' => [$attribute => 'test.png'],
                    'type' => [$attribute => 'image/jpg'],
                    'tmp_name' => [$attribute => Yii::getAlias('@app') . '/tests/codeception/_data/test.png'],
                    'size' => [$attribute => 512],
                    'error' => [$attribute => 0],
                ],
            ];
        } else {
            $_FILES = null;
        }
    }

    /**
     * privateやprotectedなメソッドを実行して返す
     * @param YiiObj $obj
     * @param string $methodName
     * @param array $arguments
     * @return mixed
     */
    protected static function method(YiiObj $obj, string $methodName, array $arguments)
    {
        $reflection = new ReflectionClass($obj::className());

        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $arguments);
    }

    /**
     * privateやprotectedなpropertyの値を返す
     * @param YiiObj $obj
     * @param $propertyName
     * @return mixed
     */
    protected static function property(YiiObj $obj, string $propertyName)
    {
        $array = (array)$obj;
        return $array["\0" . $obj::className() . "\0" . $propertyName];
    }
}
