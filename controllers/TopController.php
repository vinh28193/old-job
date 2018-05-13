<?php

namespace app\controllers;

use app\common\controllers\CommonController;
use app\models\forms\JobSearchForm;
use app\models\MainVisual;
use app\models\manage\HotJob;
use app\models\manage\SiteHtml;
use app\models\manage\HeaderFooterSetting;
use app\models\manage\SearchkeyMaster;
use Yii;
use yii\web\Cookie;
use yii\web\NotFoundHttpException;
use yii\helpers\ArrayHelper;
use app\models\WidgetLayoutDisp;
use app\models\manage\searchkey\Area;

/**
 * 全国トップ画面のコントローラ
 *
 * @author Yukinori Nakamura<y_nakamura@id-frontier.jp>
 */
class TopController extends CommonController
{

    /**
     * プレビューかどうか
     *
     * @var bool
     */
    private $_isPreview = false;

    /**
     * 全国トップアクション
     * todo area_dirをlowerCamelCaseにrename
     * @param string|null $area_dir エリアディレクトリ
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex($area_dir = null)
    {
        SearchkeyMaster::saveRouteSetting();
        $jobSearchForm = new JobSearchForm();

        // todo $_isPreviewの廃止
        if (!$this->_isPreview) {
            $area = $this->area($area_dir);
            if ($area instanceof Area) {
                // 全国orエリアトップにアクセスする場合
                $jobSearchForm->initTopScenario($area);
            } elseif (is_string($area)) {
                // クッキーでエリアにリダイレクトされる場合
                return $this->redirect(['/' . $area]);
            } else {
                throw new NotFoundHttpException();
            }
        } else {
            $area = Area::nationwideArea();
        }
        //ウィジェットデータの取得
        $widgetLayouts = $this->findModels($area->id);
        //レイアウトナンバー区切りでWidgetLayoutモデルを格納
        $widgetLayouts = ArrayHelper::index($widgetLayouts, 'widget_layout_no');

        if (!$this->isMobile) {
            $allCount = $jobSearchForm->count();
        }
        //AccessLogのGoogleAnalytics用のパラメータを挿入
        $this->isAnalytics = true;
        $this->analyticsParam = '0';

        //注目情報データの取得
        list($hotJob, $dataProvider) = self::hotJobResult($area->id);

        return $this->render('index', [
            'searchForm' => $jobSearchForm,
            'widgetLayouts' => $widgetLayouts,
            // todo $areaを渡せばいいようにviewを調整するべき
            'areaName' => $area->id && !Yii::$app->area->isOneArea() ? $area->area_name : null,
            'areaId' => $area->id,
            // todo allCountもview側で呼び出すべき
            'allCount' => $allCount ?? null,
            'hotJob' => $hotJob,
            'mainVisual' => $area->id == Area::NATIONWIDE_ID ? $this->areaNationWide() : $area->mainVisual,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 注目情報検索結果のdataProvider作成
     * @return array
     */
    public static function hotJobResult($areaId)
    {
        $hotJob = HotJob::find()->one();
        $hotJob->disp_type_ids = explode(',', $hotJob->disp_type_ids);

        $searchForm = new JobSearchForm();
        $searchForm->disp_type_sort = $hotJob->disp_type_ids;
        $dataProvider = $searchForm->searchHotJob($hotJob, $areaId);

        return [$hotJob, $dataProvider];
    }

    /**
     * cookieを消して全国トップへredirect
     *
     * @return \yii\web\Response
     */
    public function actionZenkoku()
    {
        Yii::$app->response->cookies->remove('areaDir');
        return $this->redirect('/');
    }

    /**
     * WidgetLayoutDispモデル群の取得
     *
     * @param int $areaId エリアID
     * @return array
     */
    private function findModels($areaId)
    {
        return WidgetLayoutDisp::getShowWidgetLayouts($areaId);
    }

    /**
     * アクセスに応じて値を返す
     * @param $areaDir
     * @return Area|string|null
     */
    private function area($areaDir)
    {
        // $areaDirがあればそれを元にエリアのインスタンスを返す
        // $areaDirに該当するエリアがなければnullを返す
        if ($areaDir) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return Area::find()->where(['area_dir' => $areaDir, 'valid_chk' => Area::FLAG_VALID])->one();
        }

        /** @var \app\components\Area $areaComp */
        $areaComp = Yii::$app->area;

        // ワンエリアならそのエリアのインスタンスを返す
        if ($areaComp->isOneArea()) {
            return $areaComp->firstArea;
        }

        // クッキーにareaDirがあり、それが有効なエリアならそのareaDirを文字列で返す
        // areaDirに該当するエリアがなければnullを返す
        if ($areaDir = Yii::$app->request->cookies->getValue('areaDir', null)) {
            if ($areaComp->fetchAreaByDir($areaDir)) {
                return $areaDir;
            } else {
                return null;
            }
        }

        // どれにも該当しない場合（全国エリア）は全国エリアインスタンスを返す
        return $areaComp->nationwideArea;
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    private function areaNationWide()
    {
        return MainVisual::find()
            ->where(['area_id' => null, 'valid_chk' => 1])
            ->one();
    }

    /**
     * サイト設定->ヘッダーフッター設定ページの
     * プレビュー機能
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPreview()
    {
        $headerFooter = new HeaderFooterSetting();
        if (!$headerFooter->load($this->post) || !$headerFooter->validate()) {
            throw new NotFoundHttpException;
        }

        $siteHtml = new SiteHtml();
        $siteHtml->fixHtml($headerFooter);
        $this->siteHtml = $siteHtml;
        $this->_isPreview = true;

        // スマホの場合、レイアウトファイルを切り替える
        // @see @app\modules\manage\views\secure\settings\header-footer-html\update.php::21
        if (($this->get['mode'] ?? null) === 'Mobile') {
            $this->isMobile = true;
        }

        return $this->actionIndex();
    }
}
