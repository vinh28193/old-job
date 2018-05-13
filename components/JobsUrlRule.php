<?php
namespace app\components;

use yii;
use yii\helpers\ArrayHelper;
use yii\web\UrlRuleInterface;
use app\models\manage\SearchkeyMaster;
use app\common\Helper\JmUtils;

/**
 * テナント毎に動的なURLのRoutingを設定する
 *
 * Class JobsUrlRule
 * @package app\components
 */
class JobsUrlRule implements UrlRuleInterface
{
    /**
     * 求人表示
     * @var string
     */
    public $job;
    /**
     * エリア設定
     * @var array
     */
    public $areas;
    /**
     * 検索項目設定
     * @var array
     */
    public $conditions;

    /**
     * 携帯に送る action
     * @var array
     */
    private $jobActions = [
        'send-mobile',
        'send-mobile-complete',
    ];

    /**
     * 求人検索結果Action
     * @var string
     */
    private $serachResultAction = 'search-result';

    /**
     * URLから処理先を判定する
     * @param \yii\web\UrlManager $manager
     * @param \yii\web\Request $request
     * @return array|bool
     */
    public function parseRequest($manager, $request)
    {
        // フリーワード検索に"/"が入ってきたときのために、下記のような処理にしている
        $basePaths = explode(SearchkeyMaster::FREE_WORD_PREFIX, $request->getPathInfo());
        $paths = explode('/', $basePaths[0]);

        // エリアTOP
        if (count($paths) == 1 && in_array($paths[0], array_keys($this->areas))) {
            return [
                '/top/index',
                [
                    'area_dir' => $paths[0],
                ],
            ];
        }

        // 求人関連
        if ($paths[0] == $this->job && isset($paths[1])) {
            // 携帯に送る
            if (in_array($paths[1], $this->jobActions) && is_numeric($paths[2])) {
                return [
                    "kyujin/{$paths[1]}",
                    ['job_no' => $paths[2]],
                ];
            }
            // 求人詳細
            if (is_numeric($paths[1])) {
                return [
                    'kyujin/index',
                    ['job_no' => $paths[1]],
                ];
            }
            // その他求人
            return [
                "kyujin/{$paths[1]}",
                [],
            ];
        }

        // 求人検索
        $params = [
            'areaId' => [],
            'conditions' => [],
        ];
        
        // フリーワード検索がURLに入っていた場合のみ、パラメータに入れる
        if(count($basePaths) > 1 && !JmUtils::isEmpty($basePaths[1])){
            $params['keyword'] = $basePaths[1];
        }

        foreach ($paths as $k => $path) {
            if(!JmUtils::isEmpty($path)){
                // 1階層目にエリア
                if ($k == 0 && in_array($path, array_keys($this->areas))) {
                    $params['areaId'] = $this->areas[$path];
                    continue;
                }

                if ($path == $this->serachResultAction) {
                    // 結果Actionが指定されたら結果ページへそのまま
                    continue;
                }

                // 形式チェック
                if (!preg_match('/([^\d]+)([\d,]+)/', $path, $results)) {
                    return false;
                }
                // 設定とのチェック
                if (!in_array($results[1], $this->conditions)) {
                    return false;
                }

                $params['conditions'][$results[1]] = explode(',', $results[2]);
            }
        }

        // action に json_encode して渡す(配列が渡せないので)
        return [
            "{$this->job}/{$this->serachResultAction}",
            ['params' => json_encode($params)],
        ];
    }

    /**
     * URL作成
     * 負荷対策のため、parseRequestメソッドと比べて、評価の順番を変更している
     * @param \yii\web\UrlManager $manager
     * @param string $route
     * @param array $params
     * @return string
     */
    public function createUrl($manager, $route, $params)
    {
        // KyujinControllerの場合
        if (strpos($route, $this->job) === 0) {

            $isJobNo = !is_null(ArrayHelper::getValue($params, 'job_no'))
                && is_numeric(ArrayHelper::getValue($params, 'job_no'));

            // 求人原稿詳細画面
            if ($route == $this->job . '/index' && $isJobNo) {
                return $this->job . '/' . $params['job_no'];
            }

            // 求職者原稿検索結果画面
            if ($route == "$this->job/$this->serachResultAction") {

                $searchParams = (array)json_decode(ArrayHelper::getValue($params, 'params'));
                $area = ArrayHelper::getValue(array_flip($this->areas), ArrayHelper::getValue($searchParams, 'areaId'));
                $conditions = (array)ArrayHelper::getValue($searchParams, 'conditions');
                $keyword = ArrayHelper::getValue($searchParams, 'keyword');

                $urlResultArray = [];

                foreach ($conditions AS $key => $val) {
                    if (ArrayHelper::isIn($key, $this->conditions)) {
                        $urlResultArray[] = $key . implode(',', $val);
                    }
                }

                // キーワード検索
                if ($keyword) {
                    //一回目のエンコード
                    $keyword = urlencode($keyword);
                    // スラッシュだけエンコード値に置換。("%2f"がURLに含まれている場合、サーバー設定によっては404を返すため）
                    $keyword = str_ireplace("%2f","%252f",$keyword);
                    $urlResultArray[] = SearchkeyMaster::FREE_WORD_PREFIX . $keyword;
                }

                $url = implode('/', $urlResultArray);

                $page = ArrayHelper::getValue($params, 'page');
                $sort = ArrayHelper::getValue($params, 'sort');

                return ($area ? $area . '/' : '')
                . (!$url ? $this->serachResultAction : '')
                . $url
                . ($page || $sort ? '?' : '')
                . ($page ? ('page=' . $page) : '')
                . ($page && $sort ? '&' : '')
                . ($sort ? ('sort=' . $sort) : '');
            }

            // 他のアクション（メール送信・メール送信完了画面の場合はjob_noをつける）
            if (preg_match('/' . $this->job . '\/([\S]+)/', $route, $results) && in_array($results[1], $this->jobActions) && $isJobNo) {
                return "{$this->job}/$results[1]/" . $params['job_no'];
            } else {
                return false;
            }

        } elseif ($route == "/top/index") {
            // エリアTOPページ
            $areaDir = ArrayHelper::getValue($params, 'area_dir');

            if ($areaDir) {
                return $areaDir;
            } else {
                return false;
            }

        } else {
            return false;
        }
    }
}