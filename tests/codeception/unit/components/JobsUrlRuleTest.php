<?php
namespace app\components;

use yii;
use \app\models\manage\SearchkeyMaster;
use tests\codeception\unit\JmTestCase;
use tests\codeception\fixtures\SearchkeyMasterFixture;
use tests\codeception\fixtures\AreaFixture;

/*
 * JobsUrlRuleのURL解析と、URL生成に関するテスト
 */
class JobsUrlRuleTest extends JmTestCase
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * フィクスチャ設定
     * @return array
     */
    public function fixtures()
    {
        return [
            'searchkey_master' => SearchkeyMasterFixture::className(),
            'area' => AreaFixture::className(),
        ];
    }

    /**
     * KyujinControllerに関するテストケース（ソート・ページネーション除く）
     * @return array
     */
    private function getSearchUrlParams()
    {
        return [
            'kanto' => [
                'route' => '/top/index',
                'params' => [
                    'area_dir' => 'kanto'
                ]
            ],
            'kyujin/2' => [
                'route' => 'kyujin/index',
                'params' => [
                    'job_no' => 2
                ]
            ],
            'kyujin/send-mobile/2' => [
                'route' => 'kyujin/send-mobile',
                'params' => [
                    'job_no' => 2
                ]
            ],
            'kyujin/send-mobile-complete/2' => [
                'route' => 'kyujin/send-mobile-complete',
                'params' => [
                    'job_no' => 2
                ]
            ],
            'search-result' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => [],
                    ]),
                ]
            ],
            'kanto/search-result' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => 2,
                        'conditions' => [],
                    ]),
                ]
            ],
            'PC13' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => ['PC' => ["13"]],
                    ]),
                ]
            ],
            'kanto/PC13' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => 2,
                        'conditions' => ['PC' => ["13"]]
                    ]),
                ]
            ],
            'PC13,14' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => ['PC' => ["13","14"]]
                    ]),
                ]
            ],
            'PM61' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => ['PM' => ["61"]]
                    ]),
                ]
            ],
            'PM61,88' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => ['PM' => ["61","88"]]
                    ]),
                ]
            ],
            'DC1' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => ['DC' => ["1"]]
                    ]),
                ]
            ],
            'DC1,2' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => ['DC' => ["1","2"]]
                    ]),
                ]
            ],
            'FWaa' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => [],
                        'keyword' => 'aa'
                    ]),
                ]
            ],
            'kanto/FWaa' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => 2,
                        'conditions' => [],
                        'keyword' => 'aa',
                    ]),
                ]
            ],
            'kanto/PC13,14/PM61,88/DC1,2/FWaa' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => 2,
                        'conditions' => [
                            'PC' => ["13", "14"],
                            'PM' => ["61", "88"],
                            'DC' => ["1", "2"],
                        ],
                        'keyword' => 'aa'
                    ]),
                ]
            ],
        ];
    }

    /**
     * KyujinControllerに関するテストケース（クエリパラメータ付き）
     * @return array
     */
    private function getSpAndSearchUrlParams()
    {
        return [
            'search-result?page=1' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => [],
                    ]),
                    'page' => 1,
                ]
            ],
            'search-result?sort=2' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => [],
                    ]),
                    'page' => null,
                    'sort' => 2,
                ]
            ],
            'search-result?page=1&sort=2' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => [],
                        'conditions' => [],
                    ]),
                    'page' => 1,
                    'sort' => 2,
                ]
            ],
            'kanto/PC13,14/PM61,88/DC1,2/FWaa?page=1&sort=2' => [
                'route' => 'kyujin/search-result',
                'params' => [
                    'params' => json_encode([
                        'areaId' => 2,
                        'conditions' => [
                            'PC' => ["13", "14"],
                            'PM' => ["61", "88"],
                            'DC' => ["1", "2"],
                        ],
                        'keyword' => 'aa'
                    ]),
                    'page' => 1,
                    'sort' => 2,
                ]
            ],
        ];
    }

    /**
     * \app\components\JobsUrlRule ではルーティングしないテストケース
     * @return array
     */
    private function getDefaultUrlParams()
    {
        return [
            '' => [
                'route' => 'top/index',
                'params' => []
            ],
            'kyujin/feature-search?area=2&searchkey_no=1' => [
                'route' => 'kyujin/feature-search',
                'params' => [
                    'area' => 1,
                    'searchkey_no' => 2,
                ]
            ],
            'apply/1' => [
                'route' => 'apply/index',
                'params' => []
            ],
            'ctest' => [
                'route' => 'ctest/index',
                'params' => []
            ],
            'ctest/atest' => [
                'route' => 'ctest/atest',
                'params' => []
            ],
            'mtest/ctest' => [
                'route' => 'mtest/ctest/index',
                'params' => []
            ],
            'mtest/ctest/atest' => [
                'route' => 'mtest/ctest/atest',
                'params' => []
            ],
        ];
    }


    /**
     * JobsUrlRuleの、URL解析に関するテスト
     */
    public function testParseRequest()
    {
        SearchkeyMaster::saveRouteSetting();

        $settingFile = Yii::getAlias('@runtime') . '/setting/' . $_SERVER['HTTP_HOST'] . '.json';

        $tenantRules = [];
        if (file_exists($settingFile)) {
            $tenantRules = json_decode(file_get_contents($settingFile), true);
        }

        /** @var \app\components\JobsUrlRule $jobsUrlRule */
        $jobsUrlRule = Yii::createObject('app\components\JobsUrlRule');
        $jobsUrlRule->job = $tenantRules['job'];
        $jobsUrlRule->areas = $tenantRules['areas'];
        $jobsUrlRule->conditions = $tenantRules['conditions'];

        /** @var \yii\web\UrlManager $urlManager */
        $urlManager = Yii::createObject('yii\web\UrlManager');

        /** @var \yii\web\Request $request */
        $request = Yii::createObject('yii\web\Request');

        // URL解析（KyujinController）のテスト（ソート・ページネーションなし）
        // 注：ソート・ページネーション、は正規化していないため、テストから除いている
        foreach ($this->getSearchUrlParams() AS $k => $v){
            $request->setPathInfo($k);
            verify($jobsUrlRule->parseRequest($urlManager, $request))->equals([$v['route'], $v['params']]);
        }
    }

    /**
     * JobsUrlRuleの、URL生成に関するテスト
     */
    public function testCreateUrl()
    {
        SearchkeyMaster::saveRouteSetting();

        $settingFile = Yii::getAlias('@runtime') . '/setting/' . $_SERVER['HTTP_HOST'] . '.json';

        $tenantRules = [];
        if (file_exists($settingFile)) {
            $tenantRules = json_decode(file_get_contents($settingFile), true);
        }

        /** @var \app\components\JobsUrlRule $jobsUrlRule */
        $jobsUrlRule = Yii::createObject('app\components\JobsUrlRule');
        $jobsUrlRule->job = $tenantRules['job'];
        $jobsUrlRule->areas = $tenantRules['areas'];
        $jobsUrlRule->conditions = $tenantRules['conditions'];

        /** @var \yii\web\UrlManager $urlManager */
        $urlManager = Yii::createObject('yii\web\UrlManager');

        // URL生成（KyujinController）のテスト（ソート・ページネーションなし）
        foreach ($this->getSearchUrlParams() AS $k => $v){
            verify($jobsUrlRule->createUrl($urlManager, $v['route'], $v['params']))->equals($k);
        }

        // URL生成（KyujinController）のテスト（ソート・ページネーション付き）
        foreach ($this->getSpAndSearchUrlParams() AS $k => $v){
            verify($jobsUrlRule->createUrl($urlManager, $v['route'], $v['params']))->equals($k);
        }

        // URL生成（KyujinController以外）のテスト
        foreach ($this->getDefaultUrlParams() AS $k => $v){
            verify($jobsUrlRule->createUrl($urlManager, $v['route'], $v['params']))->equals(false);
        }
    }
}