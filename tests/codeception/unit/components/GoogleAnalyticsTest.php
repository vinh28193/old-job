<?php
namespace app\components;

use tests\codeception\unit\JmTestCase;
use Yii;
use app\models\manage\AccessLogSearch;
use app\models\manage\AccessLog;

class GoogleAnalyticsTest extends JmTestCase
{
    /**
     * GoogleAnalyticsデータ取得テスト
     */
    public function testGetGoogleAnalytics()
    {
        $lastUpdateTime = AccessLog::find()->max('accessed_at');

        $googleAnalytics = Yii::$app->googleAnalytics;
        $obj = $googleAnalytics->getGoogleAnalytics();
        if (isset($obj->rows)) {
            for ($i=0, $l=count($obj->rows); $l > $i; $i++) {
                $item = $obj->rows[$i];
                // access_logテーブルの最新の日付以降のデータのみを取得している
                verify($lastUpdateTime < $item[3])->true();
            }
        }
    }

    /**
     * GoogleAnalyticsデータ取得テスト
     */
    public function testSaveGoogleAnalytics()
    {
        $lastUpdateTime = AccessLog::find()->max('accessed_at');
        
        $googleAnalyticsCount = 0;
        //GoogleAnalyticsのデータ件数を取得
        $googleAnalytics = Yii::$app->googleAnalytics;
        $obj = $googleAnalytics->getGoogleAnalytics();
        if (isset($obj->rows)) {
            $googleAnalyticsCount = count($obj->rows);
        }
        
        $googleAnalytics->saveGoogleAnalytics();
        
        // データの取得件数の確認
        $models = AccessLogSearch::find()->where(['>', 'accessed_at', $lastUpdateTime])->orderBy(['accessed_at' => SORT_ASC])->all();
        
        if (!empty($models)) {
            verify(count($models))->equals($googleAnalyticsCount);
            
            $index = 0;
            // 登録データの内容の確認
            foreach ($models as $model) {
                $item = $obj->rows[$index];

                // アクセスされた日時
                verify($model->accessed_at)->equals($item[3]);

                // 検索用の日付データ
                verify($model->search_date)->equals(date('Y-m-d', $item[3]));

                // アクセスされた機器
                if ($item[0] == 'desktop') {
                    verify($model->carrier_type)->equals(AccessLog::PC_CARRIER);
                } else {
                    verify($model->carrier_type)->equals(AccessLog::SMART_PHONE_CARRIER);
                }

                // アクセスされたURL
                verify($model->access_url)->equals($item[4]);

                // アクセスされたユーザーエージェント
                verify($model->access_user_agent)->equals($item[5]);

                // アクセスされたリファラー
                verify($model->access_referrer)->equals($item[1]);

                // アクセスされたブラウザ
                verify($model->access_browser)->equals($item[2]);

                $params = explode(',', $item[6]);
                // job_master_id
                if (!empty($params[0])) {
                    verify($model->job_master_id)->equals($params[0]);
                } else {
                    verify($model->job_master_id)->equals('');
                }
                // application_master_id
                if (!empty($params[1])) {
                    verify($model->application_master_id)->equals($params[1]);
                } else {
                    verify($model->application_master_id)->equals('');
                }
                $index++;
            }
        }
    }
}
