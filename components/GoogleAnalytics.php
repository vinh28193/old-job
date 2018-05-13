<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/08/31
 * Time: 11:20
 */

namespace app\components;

use yii\base\Component;
use app\models\manage\AccessLog;
use yii\helpers\Url;
use yii;

/**
 * Class Area
 * @package app\components
 *
 * @property \app\models\manage\searchkey\GoogleAnalytics[] $models
 */
class GoogleAnalytics extends Component
{
    /** @var \app\models\manage\searchkey\Area[] Areaのモデル配列 */
    private $_models;

    /**
     * クライアントID
     */
    public $clientId;

    /**
     * ビューID
     */
    public $viewId;

    /**
     * 秘密キー
     */
    public $privateKey;

    /**
     * トラッキングコード
     */
    public $trackingCode;

    /**
     * Google Analyticsの情報を取得する
     * @return \Google_Service_Analytics_GaData
     */
    public function getGoogleAnalytics()
    {
        // 秘密キーファイルの読み込み
        $privateKey = @file_get_contents(Yii::getAlias('@app') . '/common/googleanalytics/' . $this->privateKey);

        // 最終のアクセス時間のデータを取得する
        $lastUpdateTime = AccessLog::find()->max('accessed_at');

        if ($lastUpdateTime) {
            $from = date('Y-m-d', $lastUpdateTime); // 対象開始日
        } else {
            $from = '2005-01-01'; // 対象開始日
        }

        // 取得する期間 (YYYY-MM-DD)
        $to = date('Y-m-d'); // 対象終了日

        $currentUrl = Url::toRoute('/', true);

        // 取得するデータの組み合わせ (複数の場合は[,]で区切る)
        $dimensions = 'ga:deviceCategory,ga:dimension5, ga:browser, ga:dimension6, ga:dateHourMinute, ga:dimension3, ga:dimension1'; // ディメンション
        $metrics = 'ga:pageviews';  // メトリクス

        //オプション
        $option = [
            'dimensions' => $dimensions,
            'filters' => 'ga:dimension2=~' . $currentUrl,
            'sort' => 'ga:dateHourMinute,ga:dimension1,ga:deviceCategory,-ga:pageviews',
            // 'start-index' => 11, // 取得開始位置
        ];

        // スコープのセット (読み込みオンリー)
        $scopes = ['https://www.googleapis.com/auth/analytics.readonly'];

        // クレデンシャルの作成
        $credentials = new \Google_Auth_AssertionCredentials($this->clientId, $scopes, $privateKey);

        // Googleクライアントのインスタンスを作成
        $client = new \Google_Client();

        $client->setAssertionCredentials($credentials);

        // トークンのセット
        if (isset($_SESSION['service_token'])) {
            $client->setAccessToken($_SESSION['service_token']);
        }
        // トークンのリフレッシュ
        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($credentials);
        }

        // セッションの設定
        $_SESSION['service_token'] = $client->getAccessToken();

        // Analyticsのインスタンスを作成
        $analytics = new \Google_Service_Analytics($client);

        // データの取得
        $obj = $analytics->data_ga->get('ga:' . $this->viewId, $from, $to, $metrics, $option);

        if (isset($obj) & isset($lastUpdateTime)) {
            // 最新のデータのアクセス日付を格納
            $obj->latestAccessDate = $lastUpdateTime;
        }

        return $obj;
    }

    /**
     * GoogleAnalyticsのデータをaccess_logテーブルに登録する。
     * @return bool
     */
    public function saveGoogleAnalytics()
    {
        //Google Analyticsのデータを取得し、AccessLogテーブルに登録する
        $obj = self::getGoogleAnalytics();
        if (isset($obj->rows)) {
            for ($i=0, $l=count($obj->rows); $l > $i; $i++) {
                $insertAccessLog = new AccessLog();
                // エイリアス
                $item = $obj->rows[$i];

                // パラメータの中身を分割して取得
                $params = explode(',', $item[3]);
                if (!empty($params[0])) {
                    $dateTime = $params[0];
                    // GoogleAnalyticsの日付とずれる場合は、GoogleAnalyticsの日付を使用（スマホ_タブ対策）
                    if (date('YmdHi', $dateTime) != $item[4]) {
                        $dateTime = strtotime($item[4] . '00');
                    }
                } else {
                    $dateTime = '';
                }
                $accessUrl = (empty($params[1])) ? '' : $params[1];
                $jobMasterId = (empty($params[2])) ? '' : $params[2];
                $applicationMasterId =  (empty($params[3])) ? '' : $params[3];

                // 最新のアクセス日付以降のデータのみ登録する
                if (!isset($obj->latestAccessDate) || $obj->latestAccessDate < $dateTime) {
                    for ($j=0; $j<$item[7]; $j++) {
                        // ページビューの数だけ、データ登録
                        if ($j > 0) {
                            $insertAccessLog = new AccessLog();
                        }
                        $insertAccessLog->accessed_at = $dateTime;
                        // 検索用の日付データを挿入
                        $insertAccessLog->search_date = date('Y-m-d', $dateTime);
                        
                        if ($item[0] == 'desktop') {
                            $insertAccessLog->carrier_type = AccessLog::PC_CARRIER;
                        } else {
                            $insertAccessLog->carrier_type = AccessLog::SMART_PHONE_CARRIER;
                        }
                        $insertAccessLog->access_url = $accessUrl;

                        //job_master_id,application_master_idを取得
                        $insertAccessLog->job_master_id = $jobMasterId;
                        $insertAccessLog->application_master_id = $applicationMasterId;

                        // ユーザーエージェント、リファラーはカラムに収まらない場合を考慮
                        if (mb_strlen($item[5]) > AccessLog::MAX_LENGTH_USER_AGENT) {
                            $insertAccessLog->access_user_agent = mb_substr($item[5], 0, AccessLog::MAX_LENGTH_USER_AGENT);
                        } else {
                            $insertAccessLog->access_user_agent = $item[5];
                        }
                        if (mb_strlen($item[1]) > AccessLog::MAX_LENGTH_REFERRER) {
                            $insertAccessLog->access_referrer = mb_substr($item[1], 0, AccessLog::MAX_LENGTH_REFERRER);
                        } else {
                            $insertAccessLog->access_referrer = $item[1];
                        }
                        $insertAccessLog->access_browser = $item[2];
                        $insertAccessLog->save();
                    }
                }
            }
        }
    }
}
