<?php

namespace models\manage;

use app\components\Area as ComArea;
use app\models\manage\JobMaster;
use app\models\manage\SearchkeyMaster;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\JobPref;
use app\models\manage\searchkey\JobDist;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\PrefDistMaster;
use tests\codeception\unit\fixtures\JobDistFixture;
use tests\codeception\unit\fixtures\JobPrefFixture;
use tests\codeception\fixtures\PrefFixture;
use tests\codeception\unit\JmTestCase;
use app\models\JobMasterDisp;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ClientMasterFixture;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\db\ActiveQuery;
use tests\codeception\fixtures\DistFixture;

/**
 * @group job_master
 *
 * @property JobMasterFixture $job_master
 * @property ClientMasterFixture $client_master
 * @property PrefFixture $pref
 * @property JobPrefFixture $job_pref
 * @property DistFixture $dist
 * @property JobDistFixture $job_dist
 */
class JobMasterDispTest extends JmTestCase
{
    /**
     * フィクスチャ設定
     * @return array
     */
/*    public function fixtures()
    {
        return [
            'job_master' => JobMasterFixture::className(),
            'client_master' => ClientMasterFixture::className(),
            'pref' => PrefFixture::className(),
            'job_pref' => JobPrefFixture::className(),
            'dist' => DistFixture::className(),
            'job_dist' => JobDistFixture::className(),
        ];
    }
*/
    /**
     * 掲載企業名取得テスト
     */
    public function testGetClientName()
    {
        $jobFixtures = $this->getFixture('job_master');
        $jobData = $jobFixtures[0];
        $clientFixtures = $this->getFixture('client_master');

        $model = JobMasterDisp::findOne($jobData['id']);

        $clientMasterName = null;
        foreach ((array) $clientFixtures->data as $clientDatas){
            if($clientDatas['id'] == $model->client_master_id){
                $clientMasterName = $clientDatas['client_name'];
                break;
            }
        }
        verify($model->clientName)->equals($clientMasterName);
    }

    /**
     * 仕事情報取得テスト
     */
    public function testFindDispModel()
    {
        $jobFixtures = $this->getFixture('job_master');
        foreach ((array)$jobFixtures->data as $jobData){
            $model = JobMasterDisp::findDispModel($jobData['job_no']);
            if(isset($model)){
                //JobMasterDispモデル取得成功
                verify($model->job_no)->equals($jobData['job_no']);
            }else{
                //JobMasterDispモデル取得失敗
                verify($model)->null();
            }
        }
    }

    /**
     * 都道府県名テスト ※生成ロジック変更に伴い、testSetBreadCrumbAreaInfoで同時にテスト
     */
/*    public function testPrefNames()
    {
        $job = ArrayHelper::getValue($this->job_master->data, 26);
        $jobPrefs = $this->findRecordsByForeignKey($this->job_pref, 'job_master_id', $job['id']);
        ArrayHelper::multisort($jobPrefs, 'pref_id');
        $prefNames = [];
        foreach ($jobPrefs as $jobPref) {
            $pref = $this->findRecordById($this->pref, $jobPref['pref_id']);
            $prefNames[] = $pref['pref_name'];
        }
        verify($prefNames)->notEmpty();

        $model = JobMasterDisp::findOne($job['id']);

        verify($model->prefNames)->equals(implode('・', $prefNames));
    }
*/
    /**
     * 市区町村名テスト ※生成ロジック変更に伴い、testSetBreadCrumbAreaInfoで同時にテスト
     */
/*    public function testDistNames()
    {
        $job = ArrayHelper::getValue($this->job_master->data, 26);
        $jobDists = $this->findRecordsByForeignKey($this->job_dist, 'job_master_id', $job['id']);
        ArrayHelper::multisort($jobDists, 'dist_id');
        $distNames = [];
        foreach ($jobDists as $jobDist) {
            $dist = $this->findRecordById($this->dist, $jobDist['dist_id']);
            $distNames[] = $dist['dist_name'];
        }
        verify($distNames)->notEmpty();

        $model = JobMasterDisp::findOne($job['id']);

        verify($model->distNames)->equals(implode('・', $distNames));
    }
*/
    /**
     * 画像パス取得テスト
     */
    public function testGetImagePath()
    {
        $model = new JobMasterDisp();
//        $model->client_master_id = 1;
        $model->job_pict_0 = 'test.png';
        verify($model->getImagePath('job_pict_0'))->equals('test.png');
    }

    /**
     * 仕事ID配列を取得するテスト
     */
    public function testJobIds()
    {
        $areas = (new ComArea())->models;
        foreach ((array)$areas as $area) {
            list($prefIds, $prefNos) = Pref::prefIdsPrefNos($area);
            foreach ((array)$prefIds as $prefId) {
                $jobIds = JobMasterDisp::jobIds([$prefId]);
                foreach ((array)$jobIds as $jobId) {

                    // 有効な原稿である
                    $query = JobMasterDisp::find()->active()->where([
                        JobMaster::tableName() . '.id' => $jobId,
                    ]);
                    verify($query->exists())->true();

                    // 指定の都道府県内
                    $query = JobPref::find()->where([
                        JobPref::tableName() . '.job_master_id' => $jobId,
                        JobPref::tableName() . '.pref_id' => $prefId,
                    ]);
                    verify($query->exists())->true();
                }
            }
        }
    }

    /**
     * パンくずで使う都道府県検索URLテスト ※生成ロジック変更に伴い、testSetBreadCrumbAreaInfoで同時にテスト
     */
/*    public function testGetPrefSearchUrl()
    {
        // 91・・・紐付く都道府県が全て有効
        // 92・・・紐付く都道府県の一部が無効
        // 93・・・紐付く都道府県の全てが無効
        for ($i = 91; $i <= 93; $i++) {
            $job = JobMaster::findOne($i);
            $prefList = [];
            $areaList = [];
            foreach ($job->jobPref as $jobPref) {
                $pref = $jobPref->pref;
                if(isset($pref->area)){
                    //area_dirの配列を作成
                    $areaList[] = $pref->area->area_dir;
                }
                $prefList[] = $pref->pref_no;
            }

            $jobCnt = JobMaster::find()
            ->join('INNER JOIN', 'job_pref', ['AND', 'job_pref.job_master_id = job_master.id ', 'job_pref.tenant_id = job_master.tenant_id'])
            ->join('INNER JOIN', 'pref', ['AND', 'pref.id = job_pref.pref_id', 'pref.tenant_id = job_pref.tenant_id'])
            ->where(['job_master.id' => $i])
            ->andWhere(['not', ['pref.area_id' => null]])
            ->count();

            // 有効都道府県の数チェック
            verify(count($areaList) == $jobCnt)->true();

            $areaStr = (count($areaList) == 1) ? array_shift($areaList) : '';

            $searchCds = SearchkeyMaster::find()->select([
                'first_hierarchy_cd',
                'third_hierarchy_cd',
            ])->where(['table_name' => 'pref'])->one();

            $url = Url::to( '/' . $areaStr . '/search-result/' . $searchCds->first_hierarchy_cd . implode(',', $prefList));

            //固定
            $model = JobMasterDisp::findOne($job->id);

            //比較 PrefSearchUrlを実行した結果 equals 上で作成した欲しい結果
            verify($model->prefSearchUrl)->equals($url);
        }
    }
*/
    /**
     * パンくずで使う市区町村検索URL ※生成ロジック変更に伴い、testSetBreadCrumbAreaInfoで同時にテスト
     */
/*    public function testGetDistSearchUrl()
    {
        // 91・・・紐付く都道府県が全て有効
        // 92・・・紐付く都道府県の一部が無効
        // 93・・・紐付く都道府県の全てが無効
        for ($i = 91; $i <= 93; $i++) {
            $job = JobMaster::findOne($i);
            $distList = [];
            $areaList = [];
            foreach ($job->jobPref as $jobPref) {
                if(isset($jobPref->pref->area)){
                    //area_dirの配列を作成
                    $areaList[] = $jobPref->pref->area->area_dir;
                }
            }

            foreach ($job->jobDist as $jobDist) {
                $distList[] = $jobDist->dist->dist_cd;
            }

            $jobCnt = JobMaster::find()
            ->join('INNER JOIN', 'job_pref', ['AND', 'job_pref.job_master_id = job_master.id ', 'job_pref.tenant_id = job_master.tenant_id'])
            ->join('INNER JOIN', 'pref', ['AND', 'pref.id = job_pref.pref_id', 'pref.tenant_id = job_pref.tenant_id'])
            ->where(['job_master.id' => $i])
            ->andWhere(['not', ['pref.area_id' => null]])
            ->count();

            // 有効都道府県の数チェック
            verify(count($areaList) == $jobCnt)->true();

            $areaStr = (count($areaList) == 1) ? array_shift($areaList) : '';

            $searchCds = SearchkeyMaster::find()->select([
                'first_hierarchy_cd',
                'third_hierarchy_cd',
            ])->where(['table_name' => 'pref'])->one();

            $url = Url::to( '/' . $areaStr . '/search-result/' . $searchCds->third_hierarchy_cd . implode(',', $distList));

            //固定
            $model = JobMasterDisp::findOne($job->id);

            //比較 PrefSearchUrlを実行した結果 equals 上で作成した欲しい結果
            verify($model->distSearchUrl)->equals($url);
        }
    }
*/
    /**
     * 勤務地のエリア情報の有無の取得
     *
     * @return bool
     */
    public function testCheckPrefArea()
    {
        // 91・・・紐付く都道府県が全て有効
        // 92・・・紐付く都道府県の一部が無効
        // 93・・・紐付く都道府県の全てが無効
        for ($i = 91; $i <= 93; $i++) {
            $job = JobMaster::find()
            ->join('INNER JOIN', 'job_pref', ['AND', 'job_pref.job_master_id = job_master.id ', 'job_pref.tenant_id = job_master.tenant_id'])
            ->join('INNER JOIN', 'pref', ['AND', 'pref.id = job_pref.pref_id', 'pref.tenant_id = job_pref.tenant_id'])
            ->where(['job_master.id' => $i])
            ->andWhere(['not', ['pref.area_id' => null]]);

            //固定
            $model = JobMasterDisp::findOne($i);

            verify($model->checkPrefArea())->equals($job->exists());
        }
    }


    /**
     * パンくず用エリア情報セット＆取得テスト
     */
    public function testPrepareBreadCrumbAreaInfo()
    {
        // 91・・・複数都道府県、市町村が紐付き、都道府県がエリアを跨いでいる
        // ■エリア
        // 信越・北陸、東海、関西、中国・四国
        // ■都道府県
        // 長野、静岡、京都、奈良、島根
        // pref_no : 20, 26, 22, 29
        // ■市町村
        // dist_cd : 1391, 2408, 29201, 29204, 6401, 7211, 7561, 10448, 15106, 15210, 18206, 24212, 26343, 31401
        /** @var JobMaster $job */
        $job = JobMaster::findOne(91);
        /** @var JobMasterDisp $model */
        $model = JobMasterDisp::findDispModel($job->job_no, false);

        $searchCds = SearchkeyMaster::find()->select([
            'first_hierarchy_cd',
            'third_hierarchy_cd',
        ])->where(['table_name' => 'pref'])->one();
        $pc = $searchCds->first_hierarchy_cd;
        $dc = $searchCds->third_hierarchy_cd;

        /** @var ActiveQuery $prefQuery */
        $originalPrefQuery = Pref::find()
        ->select(['pref.pref_no', 'pref.pref_name'])
        ->join('INNER JOIN', 'job_pref', ['AND', 'job_pref.pref_id = pref.id ', 'job_pref.tenant_id = pref.tenant_id'])
        ->join('INNER JOIN', 'area', ['AND', 'pref.area_id = area.id ', 'pref.tenant_id = area.tenant_id'])
        ->where(['job_master_id' => $job->id]);

        /** @var ActiveQuery $distQuery */
        $originalDistQuery = Dist::find()
        ->select(['dist.dist_cd', 'dist.dist_name'])
        ->join('INNER JOIN', 'job_dist', 'job_dist.dist_id = dist.id')
        ->where(['job_dist.job_master_id' => $job->id]);

        /** @var ActiveQuery $prefDistQuery */
        $prefDistQuery = PrefDistMaster::find()
        ->select(['pref_dist_master.pref_id', 'pref_dist.dist_id'])
        ->join('INNER JOIN', 'pref_dist', 'pref_dist_master.id = pref_dist.pref_dist_master_id');

        // エリア情報なし
        $areaInfo = [
            'areaDir' => null,
            'prefNos' => [],
            'prefDistMasterNos' => [],
            'distCds' => [],
        ];
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $job->jobPref[0]->pref->area->area_dir . '/search-result/' . $pc . $job->jobPref[0]->pref->pref_no);
        $distUrl = Url::to( '/' . $job->jobPref[0]->pref->area->area_dir . '/search-result/' . $dc . $job->jobDist[0]->dist->dist_cd);
        verify($model->prefNames)->equals($job->jobPref[0]->pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($job->jobDist[0]->dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリのみ ※HITする場合
        $areaInfo = [
            'areaDir' => 'tokai',
            'prefNos' => [],
            'prefDistMasterNos' => [],
            'distCds' => [],
        ];
        $pref = (clone $originalPrefQuery)->andWhere(['area.area_dir' => $areaInfo['areaDir']])->one();
        $dist = (clone $originalDistQuery)->andWhere(['dist.pref_no' => $pref->pref_no])->one();
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $pc . $pref->pref_no);
        $distUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $dc . $dist->dist_cd);
        verify($model->prefNames)->equals($pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリのみ ※HITしない場合
        $areaInfo = [
            'areaDir' => 'kyusyu',
            'prefNos' => [],
            'prefDistMasterNos' => [],
            'distCds' => [],
        ];
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $job->jobPref[0]->pref->area->area_dir . '/search-result/' . $pc . $job->jobPref[0]->pref->pref_no);
        $distUrl = Url::to( '/' . $job->jobPref[0]->pref->area->area_dir . '/search-result/' . $dc . $job->jobDist[0]->dist->dist_cd);
        verify($model->prefNames)->equals($job->jobPref[0]->pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($job->jobDist[0]->dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリ、都道府県 ※HITする場合
        $areaInfo = [
            'areaDir' => 'kansai',
            'prefNos' => ['1', '2', '3', '29'],
            'prefDistMasterNos' => [],
            'distCds' => [],
        ];
        $pref = (clone $originalPrefQuery)->andWhere(['pref.pref_no' => $areaInfo['prefNos']])->one();
        $dist = (clone $originalDistQuery)->andWhere(['dist.pref_no' => $pref->pref_no])->one();
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $pc . $pref->pref_no);
        $distUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $dc . $dist->dist_cd);
        verify($model->prefNames)->equals($pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリ、都道府県 ※エリア違い HITする場合
        $areaInfo = [
            'areaDir' => 'tohoku',
            'prefNos' => ['1', '2', '3', '29'],
            'prefDistMasterNos' => [],
            'distCds' => [],
        ];
        $pref = (clone $originalPrefQuery)->andWhere(['pref.pref_no' => $areaInfo['prefNos']])->one();
        $dist = (clone $originalDistQuery)->andWhere(['dist.pref_no' => $pref->pref_no])->one();
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $job->jobPref[0]->pref->area->area_dir . '/search-result/' . $pc . $pref->pref_no);
        $distUrl = Url::to( '/' . $job->jobPref[0]->pref->area->area_dir . '/search-result/' . $dc . $dist->dist_cd);
        verify($model->prefNames)->equals($pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリ、都道府県 ※HITしない場合
        $areaInfo = [
            'areaDir' => 'kansai',
            'prefNos' => ['1','2','3','4'],
            'prefDistMasterNos' => [],
            'distCds' => [],
        ];
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $pc . $job->jobPref[0]->pref->pref_no);
        $distUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $dc . $job->jobDist[0]->dist->dist_cd);
        verify($model->prefNames)->equals($job->jobPref[0]->pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($job->jobDist[0]->dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリ、市町村 ※市町村がHITする場合
        $areaInfo = [
            'areaDir' => 'kansai',
            'prefNos' => ['1', '2', '3', '29'],
            'prefDistMasterNos' => [],
            'distCds' => ['1', '29201', '2'],
        ];
        $pref = (clone $originalPrefQuery)->andWhere(['pref.pref_no' => $areaInfo['prefNos']])->one();
        $dist = (clone $originalDistQuery)->andWhere(['dist.dist_cd' => $areaInfo['distCds']])->one();
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $pc . $pref->pref_no);
        $distUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $dc . $dist->dist_cd);
        verify($model->prefNames)->equals($pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリ、都道府県、市町村 ※市町村がHITしない場合
        $areaInfo = [
            'areaDir' => 'kansai',
            'prefNos' => ['1', '2', '3', '29'],
            'prefDistMasterNos' => [],
            'distCds' => ['1', '3', '2'],
        ];
        $pref = (clone $originalPrefQuery)->andWhere(['pref.pref_no' => $areaInfo['prefNos']])->one();
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $pc . $pref->pref_no);
        $distUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $dc . $job->jobDist[0]->dist->dist_cd);
        verify($model->prefNames)->equals($pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($job->jobDist[0]->dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリ、地域グループ ※HITする場合
        $areaInfo = [
            'areaDir' => 'kansai',
            'prefNos' => [],
            'prefDistMasterNos' => ['142'],
            'distCds' => [],
        ];
        $prefDsit = $prefDistQuery->where(['pref_dist_master.pref_dist_master_no' => $areaInfo['prefDistMasterNos']])->asArray()->all();
        $prefArr = array_column($prefDsit, 'pref_id');
        $distArr = array_column($prefDsit, 'dist_id');
        codecept_debug($distArr);
        $pref = (clone $originalPrefQuery)->andWhere(['pref.id' => $prefArr])->one();
        $dist = (clone $originalDistQuery)->andWhere(['dist.dist_cd' => $distArr])->one();
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $pc . $pref->pref_no);
        $distUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $dc . $dist->dist_cd);
        verify($model->prefNames)->equals($pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);

        // エリアディレクトリ、地域グループ ※HITしない場合
        $areaInfo = [
            'areaDir' => 'kansai',
            'prefNos' => [],
            'prefDistMasterNos' => ['213'],
            'distCds' => [],
        ];
        $model = JobMasterDisp::findDispModel($job->job_no, false);
        $model->prepareBreadCrumbAreaInfo($areaInfo);
        $prefUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $pc . $job->jobPref[0]->pref->pref_no);
        $distUrl = Url::to( '/' . $areaInfo['areaDir'] . '/search-result/' . $dc . $job->jobDist[0]->dist->dist_cd);
        verify($model->prefNames)->equals($job->jobPref[0]->pref->pref_name);
        verify($model->prefSearchUrl)->equals($prefUrl);
        verify($model->distNames)->equals($job->jobDist[0]->dist->dist_name);
        verify($model->distSearchUrl)->equals($distUrl);
    }
}