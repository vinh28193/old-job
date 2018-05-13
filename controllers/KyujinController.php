<?php

namespace app\controllers;

use app\common\mail\MailSender;
use app\common\controllers\CommonController;
use app\models\forms\JobSearchForm;
use app\models\manage\searchkey\Area;
use app\models\manage\searchkey\Dist;
use app\models\manage\searchkey\JobPref;
use app\models\manage\searchkey\JobSearchkeyItem;
use app\models\manage\searchkey\JobType;
use app\models\manage\searchkey\JobTypeSmall;
use app\models\manage\searchkey\JobWage;
use app\models\manage\searchkey\Pref;
use app\models\manage\searchkey\SearchkeyItem;
use app\models\manage\searchkey\Station;
use app\models\manage\searchkey\WageItem;
use app\models\manage\SearchkeyMaster;
use app\models\ToolMaster;
use yii;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\JobMasterDisp;
use app\models\manage\SendMailSet;
use app\models\manage\searchkey\JobDist;
use app\models\JobAccessRecommend;
use yii\web\Response;

/**
 * 求人詳細コントローラ
 *
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class KyujinController extends CommonController
{

    /** findModelする際の種別 */
    const FIND_TYPE_DETAIL   = 0;
    const FIND_TYPE_PREVIEW  = 1;
    const FIND_TYPE_SHORT    = 2;
    const FIND_TYPE_NOT_WITH = 3;
    /** findModelする際の種別 */
    const INITIAL_DISPLAY_ICONS = 5;

    /**
     * 求人検索結果Action
     *
     * @var string
     */
    private $serachResultAction = 'search-result';

    /**
     * 求人詳細アクション
     *
     * @param int $job_no 仕事ナンバー
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($job_no = null)
    {
        // スマホでもPC用レイアウトを参照
        $this->layout = 'main';

        //ID指定なしは404
        if (is_null($job_no)) {
            throw new NotFoundHttpException;
        }
        $jobMasterDisp = $this->findModel($job_no, self::FIND_TYPE_DETAIL);

        // エリア情報が1件もない場合は404
        if (! $jobMasterDisp->checkPrefArea()) {
            throw new NotFoundHttpException();
        }
        // パンくず用 遷移元URLからエリア情報を抽出してセット
        $areaInfo = $this->getAreaInfoByUrl(Yii::$app->request->getReferrer());
        $jobMasterDisp->prepareBreadCrumbAreaInfo($areaInfo);

        $dispJobAccessRecommend = null;
        // レコメンド機能に関する処理（DBの更新と、表示用モデルの習得）
        if ($jobMasterDisp) {
            $dispJobAccessRecommend = $this->jobAccessRecommend($jobMasterDisp);
        }
        //AccessLogのGoogleAnalytics用のパラメータを挿入
        $this->isAnalytics = true;
        $this->analyticsParam = $jobMasterDisp->id;

        return $this->render('@app/views/kyujin/index', [
            'jobMasterDisp'          => $jobMasterDisp,
            'isPreview'              => false,    //プレビューであるかを判別している
            'dispJobAccessRecommend' => $dispJobAccessRecommend,
        ]);
    }

    /**
     * 仕事メール転送画面
     *
     * @param int $job_no
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionSendMobile($job_no = null)
    {
        // スマホでもPC用レイアウトを参照
        $this->layout = 'main';

        //ID指定なしは404
        if (is_null($job_no)) {
            throw new NotFoundHttpException;
        }
        $jobMasterDisp = $this->findModel($job_no, self::FIND_TYPE_SHORT);

        return $this->render('@app/views/kyujin/send-mobile', [
            'jobMasterDisp' => $jobMasterDisp,
        ]);
    }

    /**
     * 仕事メール転送機能
     *
     * @throws NotFoundHttpException
     */
    public function actionSendMail()
    {
        $job_no = ArrayHelper::getValue($this->post, 'job_no');

        //ID指定なしは404
        if (empty($job_no)) {
            throw new NotFoundHttpException;
        }

        $jobMasterDisp = $this->findModel($job_no, self::FIND_TYPE_NOT_WITH);
        //ロード・バリデートに失敗時は404
        if (!$jobMasterDisp->load($this->post) || !$jobMasterDisp->validate()) {
            throw new NotFoundHttpException;
        }

        //==============================
        // メール送信処理
        //==============================
        /** @var SendMailSet $mailSet */
        $mailSet                 = SendMailSet::findOne([
            'mail_type' => SendMailSet::MAIL_TYPE_JOB_TRANSFER_MAIL,
            'mail_to'   => SendMailSet::MAIL_TO_APPLICATION,
        ]);
        $mailSet->model          = $jobMasterDisp;
        $mailSet->additionalText = $jobMasterDisp->message;

        $mailSender = new MailSender();
        $mailSender->sendAutoMail($mailSet);

        Yii::$app->session->setFlash('mailSendComplete');

        $this->redirect(Url::toRoute(['kyujin/send-mobile-complete', 'job_no' => $jobMasterDisp->job_no]));
    }

    /**
     * 仕事メール転送完了画面
     *
     * @param int $job_no 仕事No
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionSendMobileComplete($job_no = null)
    {
        // スマホでもPC用レイアウトを参照
        $this->layout = 'main';

        $mailSendComplete = Yii::$app->session->getFlash('mailSendComplete');
        //ID指定なしは404
        if (is_null($job_no)) {
            throw new NotFoundHttpException;
        } elseif (!$mailSendComplete) {
            $this->redirect('/');
        }


        return $this->render('@app/views/kyujin/send-mobile-complete', [
            'job_no' => $job_no,
        ]);
    }

    /**
     * JobMasterモデルの取得
     * ケースごとに最適なqueryを発行する
     *
     * @param int $job_no 仕事No
     * @param int $findType
     * @return JobMasterDisp
     * @throws NotFoundHttpException
     */
    protected function findModel($job_no, $findType)
    {
        /** @var JobMasterDisp|null $model */
        switch ($findType) {
            case self::FIND_TYPE_DETAIL:
                $model = JobMasterDisp::findDispModel($job_no, false);
                break;
            case self::FIND_TYPE_PREVIEW:
                $model = JobMasterDisp::findDispModel($job_no, true);
                break;
            case self::FIND_TYPE_SHORT:
                $model = JobMasterDisp::find()->findOne($job_no)->active()->one();
                break;
            case self::FIND_TYPE_NOT_WITH:
                $model = JobMasterDisp::find()->where(['job_no' => $job_no])->active()->one();
                break;
            default:
                $model = null;
                break;
        }
        if (isset($model)) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 求人プレビューアクション
     *
     * @param int $job_no 仕事ナンバー
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionPreview($job_no = null)
    {
        // スマホの場合、レイアウトファイルを切り替える
        // @see @app\modules\manage\views\secure\job\form\_form.php::76
        if (($this->get['mode'] ?? null) === 'Mobile') {
            $this->isMobile = true;
        }

        $jobMasterDisp = new JobMasterDisp();
        // プレビューの際のみ、継承元のJobModelに値をロード出来るようにルールを変更するフラグをもたせている
        $jobMasterDisp->isPreview = true;
        // 求人原稿編集画面からのプレビューの場合、postで表示情報が来るため、それぞれ代入する必要がある
        if ($this->post && $jobMasterDisp->load($this->post, 'JobMaster')) {
            $jobMasterDisp->formatCheckBoxOptions();

            // 汎用検索キーのpopulate
            for ($i = 1; $i <= 20; $i++) {
                $itemIds = ArrayHelper::getValue($this->post, "JobSearchkeyItem{$i}.itemIds");
                if ($itemIds) {
                    $jobSearchKeyItems = [];
                    /** @var SearchkeyItem $itemClass */
                    $itemClass = SearchkeyMaster::MODEL_BASE_PATH . "SearchkeyItem{$i}";
                    /** @var SearchkeyItem[] $items */
                    $items = $itemClass::find()->where(['id' => $itemIds])->all();
                    foreach ($items as $item) {
                        /** @var JobSearchkeyItem $jobItem */
                        $jobItem = Yii::createObject(SearchkeyMaster::MODEL_BASE_PATH . "JobSearchkeyItem{$i}");
                        $jobItem->populateRelation('searchKeyItem', $item);
                        $jobSearchKeyItems[] = $jobItem;
                    }
                    $jobMasterDisp->populateRelation("jobSearchkeyItem{$i}", $jobSearchKeyItems);
                }
            }

            // 職種検索キーのpopulate
            $jobTypeIds = ArrayHelper::getValue($this->post, 'JobType.itemIds');
            if ($jobTypeIds) {
                $jobTypes = [];
                $types    = JobTypeSmall::find()->where(['id' => $jobTypeIds])->all();
                foreach ($types as $type) {
                    $jobType = new JobType();
                    $jobType->populateRelation('jobTypeSmall', $type);
                    $jobTypes[] = $jobType;
                }
                $jobMasterDisp->populateRelation('jobType', $jobTypes);
            }

            // 給与検索キーのpopulate
            $wageIds = ArrayHelper::getValue($this->post, 'JobWage.itemIds');
            if ($wageIds) {
                $jobWages = [];
                $wages    = WageItem::find()->where(['id' => $wageIds])->all();
                foreach ($wages as $wage) {
                    $jobWage = new JobWage();
                    $jobWage->populateRelation('wageItem', $wage);
                    $jobWages[] = $jobWage;
                }
                $jobMasterDisp->populateRelation('jobWage', $jobWages);
            }

            // 都道府県と市区町村検索キーのpopulate
            $distIds = ArrayHelper::getValue($this->post, 'JobDist.itemIds');
            if ($distIds) {
                $dists    = Dist::find()->where(['id' => $distIds])->all();
                $jobDists = [];
                foreach ($dists AS $dist) {
                    $jobDist = new JobDist();
                    $jobDist->populateRelation('dist', $dist);
                    $jobDists[] = $jobDist;
                }
                $jobMasterDisp->populateRelation('jobDist', $jobDists);
                $prefNos  = array_unique(ArrayHelper::getColumn($dists, 'pref_no'));
                $prefs    = Pref::find()->where(['pref_no' => $prefNos])->all();
                $jobPrefs = [];
                foreach ($prefs as $pref) {
                    $jobPref = new JobPref();
                    $jobPref->populateRelation('pref', $pref);
                    $jobPrefs[] = $jobPref;
                }

                $jobMasterDisp->populateRelation('jobPref', $jobPrefs);
            }
        } elseif (!is_null($job_no)) {
            // 求人原稿一覧からのプレビューの場合、仕事IDがgetで渡され、既存の（無効も含む）原稿の情報を参照する
            $jobMasterDisp = $this->findModel($job_no, self::FIND_TYPE_PREVIEW);
        } else {
            // ID指定か原稿情報がpostされていない場合は404エラー
            throw new NotFoundHttpException;
        }

        return $this->render('@app/views/kyujin/index', [
            'jobMasterDisp'          => $jobMasterDisp,
            'isPreview'              => true,            //プレビューであるかを判別している
            'dispJobAccessRecommend' => null,            //レコメンド用の値をviewで使用しているのでnullで入れる
        ]);
    }

    /**
     * レコメンド機能に関する処理（DBの更新と、表示用モデルの習得）
     *
     * @param $jobMasterDispModel \app\models\JobMasterDisp
     * @return JobAccessRecommend
     */
    private function jobAccessRecommend($jobMasterDispModel)
    {
        $session = Yii::$app->session;

        $JobAccessRecommend     = $jobMasterDispModel->getJobAccessRecommendModel();
        $dispJobAccessRecommend = clone $JobAccessRecommend;    // 表示用に更新前の（紐付いている）JobAccessRecommendモデルをコピーしておき、それを表示する。
        $JobAccessRecommend->updateAccessedJobMasterIds($jobMasterDispModel->id, $session->get('jobAccessRecommendId'));
        $session->set('jobAccessRecommendId', $jobMasterDispModel->id);

        $session->close();

        return $dispJobAccessRecommend;
    }

    /**
     * 仕事情報検索結果ページ
     *
     * @param string $params
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSearchResult($params = null)
    {
        $searchForm           = new JobSearchForm();
        $searchForm->scenario = JobSearchForm::SCENARIO_RESULT;
        $params               = json_decode($params, true);

        // エリア判定
        if ($params['areaId']) {
            // urlにエリアが含まれている
            $searchForm->area = $params['areaId'];
        } elseif (isset($this->post['area'])) {
            // postにエリアが含まれている（リダイレクト前）
            $searchForm->area = $this->post['area'];
        } elseif (count($searchForm->areas) == 1) {
            // ワンエリア
            $areas            = $searchForm->areas;
            $area             = array_shift($areas);
            $searchForm->area = $area->id;
            $params['areaId'] = $area->id;
        } else {
            // その他
            throw new NotFoundHttpException();
        }

        // postがあった場合はpostを元にURLを生成し、リダイレクトする。
        // 無かった場合はURLを解析し、検索して検索結果画面を表示する
        if (Yii::$app->request->isPost) {
            if (($this->post['wage_category_parent'] ?? null) && ($this->post['wage_category'] ?? null)) {
                unset($this->post['wage_category_parent']);
            }

            // postをパースしpropertyにセット
            $this->parseParams($searchForm);

            // postを元にURLを生成してリダイレクト
            $searchForm->load($this->post);

            $url = $this->getRedirectUrl($searchForm);

            return $this->redirect('/' . $url . '/');

        } else {
            // URLパラメータによる検索
            $conditions = $searchForm->getConditionsFromParam($params);
            $searchForm->load($conditions);
        }

        $dataProvider = $searchForm->search();

        // ページングの際の link タグ分岐
        $conditionsCnt = $this->countConditions($conditions);
        $page = Yii::$app->request->get($dataProvider->pagination->pageParam) ?? 1;
        $currentPage = $page - 1;
        $lastPage = floor($dataProvider->totalCount / $dataProvider->pagination->pageSize);
        if ($dataProvider->totalCount == 0 || $conditionsCnt >= 3) {
            // 検索結果が0件の場合 or 検索条件が3つ以上 or ソート変更の場合は no index metaタグを追加
            Yii::$app->view->registerMetaTag([
                'name' => 'robots',
                'content' => 'noindex',
            ]);
        } elseif ($currentPage <= 0) {
            if ($lastPage > 0) {
                Yii::$app->view->registerLinkTag([
                    'rel' => 'canonical',
                    'href' => Url::canonical(),
                ]);
                // 最初のページ且つ次ページが存在する場合はnextのみ
                Yii::$app->view->registerLinkTag([
                    'rel' => 'next',
                    'href' => $dataProvider->pagination->createUrl(1, null, true),
                ]);
            }
        } elseif($lastPage > $currentPage){
            // 途中のページは prev next 両方
            Yii::$app->view->registerLinkTag([
                'rel' => 'prev',
                'href' => $dataProvider->pagination->createUrl($currentPage - 1 , null, true),
            ]);
            Yii::$app->view->registerLinkTag([
                'rel' => 'next',
                'href' => $dataProvider->pagination->createUrl($currentPage + 1 , null, true),
            ]);
        } else {
            // 最後のページはprevのみ
            Yii::$app->view->registerLinkTag([
                'rel' => 'prev',
                'href' => $dataProvider->pagination->createUrl($lastPage - 1, null, true),
            ]);
        }

        // 検索件数による分岐
        switch ($conditionsCnt) {
            case 0:
                $this->site->toolNo     = ToolMaster::TOOLNO_MAP['searchResult'];
                $this->site->searchname = '';
                $breadcrumbs            = [['label' => Yii::t('app', '求人検索結果'), 'url' => Yii::$app->request->url]];
                break;
            case 1:
                $this->site->toolNo     = ToolMaster::TOOLNO_MAP['searchResultOne'];
                $this->site->searchname = implode('・', $this->searchNames($conditions));
                $breadcrumbs            = $this->breadcrumbs($conditions, $searchForm);
                break;
            case 2:
                $this->site->toolNo     = ToolMaster::TOOLNO_MAP['searchResultTwo'];
                $this->site->searchname = implode('・', $this->searchNames($conditions));
                $breadcrumbs            = $this->breadcrumbs($conditions, $searchForm);
                break;
            default:
                $this->site->toolNo     = ToolMaster::TOOLNO_MAP['searchResultOther'];
                $this->site->searchname = [];
                $breadcrumbs            = [['label' => Yii::t('app', '求人検索結果'), 'url' => Yii::$app->request->url]];
                break;
        }

        return $this->render('search-result', [
            'searchForm'   => $searchForm,
            'dataProvider' => $dataProvider,
            'conditions'   => $conditions,
            'breadcrumbs'  => $breadcrumbs,
            'conditionsCnt' => $conditionsCnt,
        ]);
    }

    /**
     * 検索結果総数を返す
     *
     * @return array
     */
    public function actionSearchCount()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $searchForm = new JobSearchForm();
        // 結果と同じに
        $searchForm->scenario = JobSearchForm::SCENARIO_RESULT;

        if (($this->post['wage_category_parent'] ?? null) && ($this->post['wage_category'] ?? null)) {
            unset($this->post['wage_category_parent']);
        }
        $this->parseParams($searchForm);
        $searchForm->load($this->post);

        return [
            'count' => (int)$searchForm->count(),
        ];
    }

    /**
     * 詳細検索
     *
     * @return string
     */
    public function actionSearchDetail()
    {
        $searchForm           = new JobSearchForm();
        $searchForm->scenario = JobSearchForm::SCENARIO_DETAIL;

        // エリア判定
        if (isset($this->post['area'])) {
            // postにエリアが含まれている
            $searchForm->area = $this->post['area'];
        } elseif (count($searchForm->areas) == 1) {
            // ワンエリア
            $areas            = $searchForm->areas;
            $area             = array_shift($areas);
            $searchForm->area = $area->id;
            $params['areaId'] = $area->id;
        } else {
            // その他
            $searchForm = $this->getAreaCache($searchForm);
        }

        // postをパースしpropertyにセット
        $this->parseParams($searchForm);

        $searchForm->load($this->post);



        if (!$this->isMobile) {
            $allCount = $searchForm->count();
        }

        return $this->render('search-detail', [
            'allCount'   => $allCount ?? null,
            'searchForm' => $searchForm,
        ]);
    }

    /**
     * 都道府県ごとの地域グループ選択画面を非同期で表示する
     *
     * @param $prefNo
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAjaxArea($prefNo)
    {
        $pref = Pref::find()
            ->with('dispPrefDistMasters.districts')
            ->where(['pref_no' => $prefNo])
            ->one();

        if (Yii::$app->request->isAjax && $pref) {
            $searchForm           = new JobSearchForm();
            $searchForm->scenario = JobSearchForm::SCENARIO_DETAIL;

            $this->parseParams($searchForm);

            $searchForm->load($this->post);

            return $this->render('search/_area-overlay-pref', [
                'searchForm' => $searchForm,
                'pref'       => $pref,
            ]);
        }
        throw new NotFoundHttpException();
    }

    /**
     * 都道府県ごとの駅選択画面を非同期で表示する
     *
     * @param $prefNo
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionAjaxStation($prefNo)
    {
        $pref = Pref::findOne(['pref_no' => $prefNo]);

        if (Yii::$app->request->isAjax && $pref) {
            $searchForm           = new JobSearchForm();
            $searchForm->scenario = JobSearchForm::SCENARIO_DETAIL;

            $this->parseParams($searchForm);

            $searchForm->load($this->post);

            $pref = Pref::findOne(['pref_no' => $prefNo]);

            return $this->render('search/_station-overlay-pref', [
                'searchForm' => $searchForm,
                'pref'       => $pref,
            ]);
        }
        throw new NotFoundHttpException();

    }

    /**
     * 検索POST情報からリダイレクトURLを作成する
     *
     * @param JobSearchForm $jobSearchForm
     * @return string
     */
    private function getRedirectUrl(JobSearchForm $jobSearchForm)
    {
        $prefFinished = false;
        $areaResult   = [];
        $results      = [];
        $areaDir      = '';
        $areaModel    = null;
        foreach ($jobSearchForm->searchKeys as $key) {
            // エリア
            if ($key->isArea && $jobSearchForm->area) {
                /** @var Area $area */
                $areaModel = ArrayHelper::getValue(array_values(array_filter($jobSearchForm->areas,
                    function (Area $area) use ($jobSearchForm) {
                        return $area->id == $jobSearchForm->area;
                    })),
                    0,
                    ''
                );
                if ($areaModel) {
                    $areaDir = $areaModel->area_dir;
                }
            }

            // 詳細勤務地検索
            if ($key->isPref && !$prefFinished) {

                $selectedDistricts = [];
                if ($key->first_hierarchy_cd && $jobSearchForm->pref) {
                    $results[] = $key->first_hierarchy_cd . implode(',', (array)$jobSearchForm->pref);
                }
                if ($key->second_hierarchy_cd && $jobSearchForm->pref_dist_master_parent) {
                    $results[] = $key->second_hierarchy_cd . implode(',', array_values((array)$jobSearchForm->pref_dist_master_parent));
                }
                $items = [];
                if (is_array($jobSearchForm->pref_dist_master)) {
                    $items = $jobSearchForm->pref_dist_master;
                } elseif ($jobSearchForm->pref_dist_master) {
                    $items = [$jobSearchForm->pref_dist_master];
                }
                $districtAllIds = @array_diff($items ?: [], array_unique((array)$selectedDistricts));
                if ($districtAllIds && $key->third_hierarchy_cd) {
                    // 市区町村コード
                    $results[] = $key->third_hierarchy_cd . implode(',', array_unique($districtAllIds));
                }
                $prefFinished = true;
            }

            // 路線検索
            if ($key->isStation) {
                if ($key->first_hierarchy_cd && $jobSearchForm->station_parent) {
                    $results[] = $key->first_hierarchy_cd . implode(',', (array)$this->post['station_parent']);
                }
                if ($key->second_hierarchy_cd && $jobSearchForm->station) {
                    // 駅コード
                    $results[] = $key->second_hierarchy_cd . implode(',', $jobSearchForm->station);
                }
            }

            // 給与
            if ($key->isWage) {
                if ($key->first_hierarchy_cd && $jobSearchForm->wage_category_parent) {
                    $results[] = $key->first_hierarchy_cd . implode(',', (array)$jobSearchForm->wage_category_parent);
                } elseif ($key->second_hierarchy_cd && $jobSearchForm->wage_category) {
                    $results[] = $key->second_hierarchy_cd . implode(',', (array)$jobSearchForm->wage_category);
                }
            }

            // 職種
            if ($key->isJobType) {
                // 職種カテゴリ全選択
                $jobCategories = Yii::$app->request->post('job_type_category_all');
                // 職種大全選択
                $jobBigTypes = Yii::$app->request->post('job_type_big_all');
                // 職種小
                $jobTypes = Yii::$app->request->post('job_types');

                $selectedJobTypes = [];
                if ($jobCategories && $key->first_hierarchy_cd) {
                    foreach ((array)$jobCategories as $category) {
                        // カテゴリ選択済みの職種を記録
                        if ($jobBigTypes && array_key_exists($category, $jobBigTypes) && $jobBigTypes[$category]) {
                            foreach ($jobBigTypes[$category] as $jobBigType) {
                                $selectedJobTypes = array_merge($selectedJobTypes, explode(',', $jobTypes[$jobBigType]));
                            }
                            // 職種カテゴリ全選択の職種大全選択は除く
                            if (isset($jobBigTypes[$category])) {
                                unset($jobBigTypes[$category]);
                            }
                        }
                    }
                    $results[] = $key->first_hierarchy_cd . implode(',', (array)$jobCategories);
                }
                if ($jobBigTypes && $key->second_hierarchy_cd) {
                    $jobBigTypeResults = [];
                    foreach ($jobBigTypes as $k => $jobBigType) {
                        $jobBigTypeResults = array_merge($jobBigTypeResults, $jobBigType);
                        foreach ($jobBigType as $jobType) {
                            if ($jobTypes && array_key_exists($jobType, $jobTypes) && $jobTypes[$jobType]) {
                                $selectedJobTypes = array_merge($selectedJobTypes, explode(',', $jobTypes[$jobType]));
                            }
                        }
                    }
                    $results[] = $key->second_hierarchy_cd . implode(',', (array)$jobBigTypeResults);
                }
                $items = [];
                if (is_array($jobSearchForm->{$key->table_name})) {
                    $items = $jobSearchForm->{$key->table_name};
                } elseif ($jobSearchForm->{$key->table_name}) {
                    $items = [$jobSearchForm->{$key->table_name}];
                }
                $jobTypeSmallIds = @array_diff($items, $selectedJobTypes);
                if ($jobTypeSmallIds && $key->third_hierarchy_cd) {
                    // 職種大全選択
                    $results[] = $key->third_hierarchy_cd . implode(',', array_unique((array)$jobTypeSmallIds));
                }
            }

            // 自由検索項目
            if ($key->isCategory) {
                $parentName = "{$key->table_name}_parent";

                // カテゴリ全選択
                $categories = Yii::$app->request->post($parentName);
                // 少項目
                $items = Yii::$app->request->post($key->table_name . '_items');

                if (empty($key->first_hierarchy_cd) && empty($key->second_hierarchy_cd)) {
                    continue;
                }

                $selectedItems = [];
                if ($jobSearchForm->{$parentName} && $key->first_hierarchy_cd) {
                    foreach ((array)$categories as $category) {
                        if (!empty($items[$category])) {
                            $selectedItems = array_merge($selectedItems, explode(',', $items[$category]));
                        }
                        // カテゴリ全選択は除く
                        if (isset($items[$category])) {
                            unset($items[$category]);
                        }
                    }
                    $results[] = $key->first_hierarchy_cd . implode(',', (array)$jobSearchForm->{$parentName});
                }

                $items = [];
                if (is_array($jobSearchForm->{$key->table_name})) {
                    $items = $jobSearchForm->{$key->table_name};
                } elseif ($jobSearchForm->{$key->table_name}) {
                    $items = [$jobSearchForm->{$key->table_name}];
                }

                $itemAllIds = @array_diff($items ?: [], array_unique((array)$selectedItems));
                if (!empty($itemAllIds) && $key->second_hierarchy_cd) {
                    $results[] = $key->second_hierarchy_cd . implode(',', $itemAllIds);
                }
            }
            if ($key->isItem) {
                if (empty($key->first_hierarchy_cd)) {
                    continue;
                }
                if (!empty($jobSearchForm->{$key->table_name})) {
                    $results[] = $key->first_hierarchy_cd . implode(',', (array)$jobSearchForm->{$key->table_name});
                }
            }
        }

        // キーワード検索をURLに反映させるための処理
        if ($jobSearchForm->keyword) {

            //一回目のエンコード
            $keyword = urlencode($jobSearchForm->keyword);

            //二回目。スラッシュだけエンコード値に置換。("%2f"がURLに含まれている場合、サーバー設定によっては404を返すため）
            $keyword = str_ireplace("%2f", "%252f", $keyword);

            $results[] = SearchkeyMaster::FREE_WORD_PREFIX . $keyword;
        }

        //エリア以外何も検索条件ないとき、'search-result'をつける
        if ($areaDir) {
            $areaResult = [$areaModel->area_dir . (!$results ? '/' . $this->serachResultAction : '')];
        }

        // エリアを再優先に表示するため、別処理に分けている。
        $results = array_merge($areaResult, $results);

        return implode('/', $results);
    }

    /**
     * @param JobSearchForm $jobSearchForm
     * @return JobSearchForm
     */
    private function getAreaCache(JobSearchForm $jobSearchForm)
    {
        $area_dir = Yii::$app->request->cookies->getValue('areaDir', null);
        if ($area_dir) {
            $areaOne = Area::findOne(['area_dir' => $area_dir]);
            if (isset($areaOne)) {
                $jobSearchForm->area = $areaOne->id;
            }
        } else {
            /** @var null|Area[] $areas */
            $areas = Area::find()
                ->where(['valid_chk' => Area::FLAG_VALID])
                ->all();
            if (count($areas) == 1) {
                $area                = ArrayHelper::getValue($areas, 0);
                $jobSearchForm->area = $area->id;
            }
        }
        return $jobSearchForm;
    }

    /**
     * ぱんくず配列取得
     * todo URLの生成規則見直し
     *
     * @param array         $conditions
     * @param JobSearchForm $searchForm
     * @return array
     */
    private function breadcrumbs($conditions, $searchForm)
    {
        $result = [];
        foreach ($conditions as $attribute => $condition) {
            if ($attribute == 'area') {
                continue;
            } elseif ($attribute == 'keyword') {
                $result[] = [
                    'label' => $condition,
                    'url'   => Url::to([
                        '/kyujin/search-result/',
                        'params' => json_encode([
                            'areaId'     => $conditions['area'],
                            'conditions' => ['keyword' => [$condition]],
                        ]),
                    ]),
                ];
            } elseif ($searchForm->attributeToCode) {
                $code = $searchForm->attributeToCode[$attribute];
                foreach ($condition as $name => $number) {
                    $result[] = [
                        'label' => $name,
                        'url'   => Url::to([
                            '/kyujin/search-result/',
                            'params' => json_encode([
                                'areaId'     => $conditions['area'],
                                'conditions' => [$code => [$number]],
                            ]),
                        ]),
                    ];
                }
            }
        }
        return $result;
    }

    /**
     * ToolMaster用の配列取得
     *
     * @param array $conditions
     * @return array
     */
    private function searchNames($conditions)
    {
        $result = [];
        foreach ($conditions as $attribute => $condition) {
            if ($attribute == 'area') {
                continue;
            } elseif ($attribute == 'keyword') {
                $result[] = $condition;
            } else {
                foreach ($condition as $name => $number) {
                    $result[] = $name;
                }
            }
        }
        return $result;
    }

    /**
     * 検索条件の数を返す
     *
     * @param array $conditions
     * @return int
     */
    private function countConditions($conditions)
    {
        if (isset($conditions['keyword'])) {
            return count($conditions, COUNT_RECURSIVE) - count($conditions) + 1;
        } else {
            return count($conditions, COUNT_RECURSIVE) - count($conditions);
        }
    }

    /**
     * カンマ区切りのパラメータを分解してJobSearchFormでloadできる状態でpostにセットする
     * 入力無しと空文字の場合は何もしない（0の場合は不正なpostなのでそれも無視して問題ない）
     *
     * @param $attribute
     */
    private function setNumbersFromString($attribute)
    {
        if ($this->post["{$attribute}_string"] ?? null) {
            $this->post["{$attribute}"] = explode(',', $this->post["{$attribute}_string"]);
        }
    }

    /**
     * postパラメータをjobSearchFormがloadできる形にパースしてpropertyのpostにセットする
     *
     * @param JobSearchForm $searchForm
     */
    private function parseParams($searchForm)
    {
        foreach ($searchForm->searchKeys as $searchKey) {
            // 2階層dropdown（ラベル選択可）の処理
            // 表示形式：dropdown、かつカテゴリ選択：可の場合
            if ($searchKey->search_input_tool == SearchkeyMaster::SEARCH_INPUT_TOOL_DROPDOWN
                && $searchKey->is_category_label == SearchkeyMaster::CATEGORY_SELECTABLE
            ) {
                $item = ArrayHelper::getValue($this->post, $searchKey->table_name);

                if (is_string($item)) {
                    if (strstr($item, JobSearchForm::DROPDOWN_CATE_PREFIX)) {
                        //カテゴリ（1階層目の項目）を選択している場合
                        $item                                           = str_replace(JobSearchForm::DROPDOWN_CATE_PREFIX, '', $item);
                        $this->post[$searchKey->table_name . '_parent'] = $item;
                        $this->post[$searchKey->table_name]             = '';
                    } elseif (strstr($item, JobSearchForm::DROPDOWN_ITEM_PREFIX)) {
                        //アイテム（2階層目の項目）を選択している場合
                        $item                               = str_replace(JobSearchForm::DROPDOWN_ITEM_PREFIX, '', $item);
                        $this->post[$searchKey->table_name] = $item;
                    }
                }
            }

            // stringパラメータのパース
            if ($searchKey->isPref) {
                if ($this->post['pref_string'] ?? null) {
                    foreach (explode(',', $this->post['pref_string']) as $num) {
                        $this->post['pref'][$searchForm->prefs[$num]->pref_name] = $num;
                    }
                }
                if ($this->post['pref_dist_master_parent_string'] ?? null) {
                    foreach (explode(',', $this->post['pref_dist_master_parent_string']) as $num) {
                        $this->post['pref_dist_master_parent'][$searchForm->prefDistricts[$num]->pref_dist_name] = $num;
                    }
                }
                if ($this->post['pref_dist_master_string'] ?? null) {
                    $nums  = explode(',', $this->post['pref_dist_master_string']);
                    $dists = Dist::findAll(['dist_cd' => $nums]);
                    foreach ($dists as $dist) {
                        $this->post['pref_dist_master'][$dist->dist_name] = $dist->dist_cd;
                    }
                }
            } elseif ($searchKey->isStation) {
                if ($this->post['station_parent_string'] ?? null) {
                    $nums = explode(',', $this->post['station_parent_string']);
                    /** @var Station[] $routes */
                    $routes = Station::find()->select(['route_cd', 'route_name'])->where(['route_cd' => $nums])->distinct()->all();
                    foreach ($routes as $route) {
                        $this->post['station_parent'][$route->route_name] = $route->route_cd;
                    }
                }
                if ($this->post['station_string'] ?? null) {
                    $nums = explode(',', $this->post['station_string']);
                    /** @var Station[] $routes */
                    $routes = Station::find()->select(['station_no', 'station_name'])->where(['station_no' => $nums])->distinct()->all();
                    foreach ($routes as $route) {
                        $this->post['station'][$route->station_name] = $route->station_no;
                    }
                }
            } elseif ($searchKey->isWage) {
                $this->setNumbersFromString('wage_category_parent');
                $this->setNumbersFromString('wage_category');
            } elseif ($searchKey->isCategory) {
                $this->setNumbersFromString("{$searchKey->table_name}_parent");
                $this->setNumbersFromString("{$searchKey->table_name}");
            } elseif ($searchKey->isItem) {
                $this->setNumbersFromString("{$searchKey->table_name}");
            }
        }

        // 優先キーのパース
        if ($this->post['principal_string'] ?? null) {
            $this->post[$searchForm->principalKey->table_name] = explode(',', $this->post['principal_string']);
        }
        if ($this->post['principal_parent_string'] ?? null) {
            $this->post[$searchForm->principalKey->table_name . '_parent'] = explode(',', $this->post['principal_parent_string']);
        }
    }

    /**
     * URLよりパンくず用、エリア情報（エリアディレクトリ、都道府県コード、地域グループコード、市町村コード）を取得
     *
     * @return array
     */
    private function getAreaInfoByUrl($url)
    {
        $areaInfo = [
            'areaDir' => null,
            'prefNos' => [],
            'prefDistMasterNos' => [],
            'distCds' => [],
        ];
        $basePaths = explode(SearchkeyMaster::FREE_WORD_PREFIX, $url);
        $paths = explode('/', $basePaths[0]);

        // エリアディレクトリ一覧取得
        $areaDirs = Area::findAllAreaDir();

        // 勤務地の接頭語情報を取得
        /** @var SearchkeyMaster $prefSearchKeyMaster */
        $prefSearchKeyMaster = SearchkeyMaster::find()->where(['table_name' => 'pref'])->one();

        // エリアディレクトリ
        foreach ($areaDirs as $areaDir) {
            if (in_array($areaDir, $paths)) {
                $areaInfo['areaDir'] = $areaDir;
                break;
            }
        }

        // 都道府県コード
        if (preg_match('/' . $prefSearchKeyMaster->first_hierarchy_cd . '([\d,]+)/', $basePaths[0], $arrayResult)) {
            $areaInfo['prefNos'] = explode(',', $arrayResult[1]);
        }

        // 地域グループコード
        if (preg_match('/' . $prefSearchKeyMaster->second_hierarchy_cd . '([\d,]+)/', $basePaths[0], $arrayResult)) {
            $areaInfo['prefDistMasterNos'] = explode(',', $arrayResult[1]);
        }

        // 市町村コード
        if (preg_match('/' . $prefSearchKeyMaster->third_hierarchy_cd . '([\d,]+)/', $basePaths[0], $arrayResult)) {
            $areaInfo['distCds'] = explode(',', $arrayResult[1]);
        }

        return $areaInfo;
    }
}
