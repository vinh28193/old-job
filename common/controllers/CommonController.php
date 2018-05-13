<?php

namespace app\common\controllers;

use app\common\Site;
use app\models\manage\SiteHtml;
use Yii;
use yii\web\Controller;

/**
 * Class CommonController
 *
 * @package app\controllers
 */
class CommonController extends Controller
{
    /**
     * POST
     * @var array
     */
    public $post;

    /**
     * GET
     * @var array
     */
    public $get;

    /**
     * @var Site
     */
    public $site;

    /**
     * スマートフォンからのアクセスか否か
     *
     * @var bool
     */
    public $isMobile = false;

    /**
     * ヘッダーフッターファイル
     * @var SiteHtml
     */
    public $siteHtml;

    /**
     * accesslogのAnalytics対応か否か
     * @var bool
     */
    public $isAnalytics;

    /**
     * accesslogのAnalytics用パラメータ
     * @var string
     */
    public $analyticsParam;

    /**
     * @inheritdoc
     */
    public function init()
    {

        parent::init();
        $this->post = Yii::$app->request->post();
        $this->get = Yii::$app->request->get();

        // 共通Componentをセットする
        $this->site = Yii::$app->site;

        // SPアクセス判定
        if (preg_match('/(iPhone|Android(.*)Mobile)/', Yii::$app->request->userAgent)) {
            $this->isMobile = true;
        }
    }

    /**
     * Viewのレンダリング
     *
     * @param string $view
     * @param array  $params
     * @return string
     */
    public function render($view, $params = [])
    {
        // SPアクセス（またはプレビューなどSPとしてアクセスする場合）レイアウトファイルを切り替える
        if ($this->isMobile) {
            // SP向けView/Layout
            $spViewPath = $this->viewPath . DIRECTORY_SEPARATOR . 'sp';
            if (file_exists($spViewPath)) {
                $this->viewPath = $spViewPath;
            }
            $this->layout = 'sp/main';
        }

        // プレビュー機能で設定されてた場合は処理を省略
        if(!isset($this->siteHtml)){
            // todo view側で代入しておくべき
            $this->siteHtml = $this->findSiteHtml();
        }

        // GoogleAnalyticsで使用するパラメータを送る
        if (isset($this->isAnalytics)) {
            $this->view->params['isAnalytics'] = $this->isAnalytics;
            $this->view->params['analyticsParam'] = $this->analyticsParam;
        }

        $this->view->params['siteHtml'] = $this->siteHtml;
        return parent::render($view, array_merge($params, [
            'site'     => $this->site,
            'isMobile' => $this->isMobile,
        ]));
    }

    /**
     * SiteHtmlモデルの取得
     * @return SiteHtml モデル
     */
    private function findSiteHtml()
    {
        /** @var null|SiteHtml $model */
        $model = SiteHtml::find()->one();
        if (isset($model)) {
            return $model;
        } else {
            return new SiteHtml;
        }
    }
}