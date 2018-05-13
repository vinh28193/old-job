<?php
use tests\codeception\fixtures\AreaFixture;
use tests\codeception\unit\fixtures\MainVisualFixture;
use tests\codeception\unit\fixtures\MainVisualImageFixture;
use tests\codeception\unit\JmTestCase;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;

/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/07/22
 * Time: 11:20
 */
class FixtureExtension extends \Codeception\Platform\Extension
{
    use \yii\test\FixtureTrait;

    /** 無視するfixture */
    const EXCEPT_FIXTURES = [
//        'Tenant',
//        'AccessLog',
    ];

    /** @var array イベントとメソッドの紐づけ */
    public static $events = [
        'suite.before' => 'beforeSuite',
    ];

    /**
     * FixtureControllerから転記
     * @var string default namespace to search fixtures in
     */
    public $namespace = 'tests\codeception\unit\fixtures';
    /**
     * FixtureControllerから転記
     * @var array global fixtures that should be applied when loading and unloading. By default it is set to `InitDbFixture`
     * that disables and enables integrity check, so your data can be safely loaded.
     */
    public $globalFixtures = [
        'yii\test\InitDbFixture',
    ];

    /**
     * unitの時のみsuite実行前にtest dbにunitのfixtureをloadする
     * 共通fixtureとunit fixtureのどちらもloadした場合にはunitが優先される
     * @param \Codeception\Event\SuiteEvent $e
     */
    public function beforeSuite(\Codeception\Event\SuiteEvent $e)
    {
        if ($e->getSuite()->getName() == 'unit') {
            // 外部キー制約の都合で先に削除する
            (new MainVisualImageFixture())->unload();
            (new MainVisualFixture())->unload();
            (new AreaFixture())->unload();

            $fixturesObjects = $this->allFixtures();

            echo "loading fixtures...\n\n";

            foreach ($fixturesObjects as $fixturesObject) {
                $fixturesObject->initTable();
            }
        }
    }

    /**
     * unitのfixtureとacceptanceとunit共通のfixtureをそれぞれ返す
     * @return \tests\codeception\JmFixture[]
     * @throws Exception
     */
    private function allFixtures()
    {
        // unit fixture
        $unitFixtureNames = $this->findFixtures();
        $unitFixtureNames = $this->filterFixture($unitFixtureNames);
        $this->namespace = 'tests\codeception\unit\fixtures';
        $unitFixtures = $this->getFixturesConfig($unitFixtureNames);

        // acceptanceとunit共通のfixture
        $acceptanceFixtureNames = $this->filterFixture(JmTestCase::COMMON_FIXTURES);
        $this->namespace = 'tests\codeception\fixtures';
        $commonFixtures = $this->getFixturesConfig($acceptanceFixtureNames);

        $fixtures = array_merge($commonFixtures, $unitFixtures);

        return $this->createFixtures($fixtures);
    }

    /**
     * 読み込まないfixtureを除外する
     * @param $fixtureNames
     * @return array
     */
    private function filterFixture($fixtureNames)
    {
        return array_filter($fixtureNames, function ($v) {
            return !in_array($v, self::EXCEPT_FIXTURES);
        });
    }

    /**
     * FixtureControllerから転記
     * Finds fixtures to be loaded, for example "User", if no fixtures were specified then all of them
     * will be searching by suffix "Fixture.php".
     * @param array $fixtures fixtures to be loaded
     * @return array Array of found fixtures. These may differ from input parameter as not all fixtures may exists.
     */
    private function findFixtures(array $fixtures = [])
    {
        $fixturesPath = $this->getFixturePath();

        $filesToSearch = ['*Fixture.php'];
        $findAll = ($fixtures === []);

        if (!$findAll) {
            $filesToSearch = [];

            foreach ($fixtures as $fileName) {
                $filesToSearch[] = $fileName . 'Fixture.php';
            }
        }

        $files = FileHelper::findFiles($fixturesPath, ['only' => $filesToSearch]);
        $foundFixtures = [];

        foreach ($files as $fixture) {
            $foundFixtures[] = $this->getFixtureRelativeName($fixture);
        }

        return $foundFixtures;
    }

    /**
     * FixtureControllerから転記
     * Returns valid fixtures config that can be used to load them.
     * @param array $fixtures fixtures to configure
     * @return array
     */
    private function getFixturesConfig($fixtures)
    {
        $config = [];

        foreach ($fixtures as $fixture) {
            $isNamespaced = (strpos($fixture, '\\') !== false);
            // replace linux' path slashes to namespace backslashes, in case if $fixture is non-namespaced relative path
            $fixture = str_replace('/', '\\', $fixture);
            $fullClassName = $isNamespaced ? $fixture . 'Fixture' : $this->namespace . '\\' . $fixture . 'Fixture';

            if (class_exists($fullClassName)) {
                $config[] = $fullClassName;
            }
        }

        return $config;
    }

    /**
     * FixtureControllerから転記
     * Calculates fixture's name
     * Basically, strips [[getFixturePath()]] and `Fixture.php' suffix from fixture's full path
     * @see getFixturePath()
     * @param string $fullFixturePath Full fixture path
     * @return string Relative fixture name
     */
    private function getFixtureRelativeName($fullFixturePath)
    {
        $fixturesPath = FileHelper::normalizePath($this->getFixturePath());
        $fullFixturePath = FileHelper::normalizePath($fullFixturePath);

        $relativeName = substr($fullFixturePath, strlen($fixturesPath) + 1);
        $relativeDir = dirname($relativeName) === '.' ? '' : dirname($relativeName) . DIRECTORY_SEPARATOR;

        return $relativeDir . basename($fullFixturePath, 'Fixture.php');
    }

    /**
     * FixtureControllerから転記
     * Returns fixture path that determined on fixtures namespace.
     * @throws InvalidConfigException if fixture namespace is invalid
     * @return string fixture path
     */
    private function getFixturePath()
    {
        try {
            return Yii::getAlias('@' . str_replace('\\', '/', $this->namespace));
        } catch (InvalidParamException $e) {
            throw new InvalidConfigException('Invalid fixture namespace: "' . $this->namespace . '". Please, check your FixtureController::namespace parameter');
        }
    }
}