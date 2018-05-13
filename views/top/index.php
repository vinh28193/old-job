<?php

use app\assets\AutoHeightAsset;
use app\models\manage\WidgetLayout;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\BootstrapPluginAsset;
use app\models\manage\searchkey\Area;
use app\models\ToolMaster;

/* @var $widgetLayouts WidgetLayout[] */
/* @var $this yii\web\View */
/* @var $areaName string */
/* @var $searchForm \app\models\forms\JobSearchForm */
/* @var $isMobile boolean */
/* @var $areaId int */
/* @var $allCount int */
/* @var $mainVisual \app\models\MainVisual */

//offcanvasとslickの競合によるレイアウト崩れ修正
$css = <<<CSS
body {
width: 100%;
}
CSS;
$this->registerCss($css);

Yii::$app->site->areaname = $areaName;
Yii::$app->site->toolNo = $areaName ? ToolMaster::TOOLNO_MAP['areatop'] : ToolMaster::TOOLNO_MAP['top'];
// todo 三項演算子入れ子はやめたい
$title = $areaName ? Yii::t('app', '{areaName}トップ', ['areaName' => $areaName]) : (Area::isOneArea() ? '' : Yii::t('app', '全国トップ'));
$this->params['breadcrumbs'][] = $title;
$this->params['bodyId'] = $areaId ? 'areaTop' : 'zenkokuTop';
$this->params['bodyClass'] = 'type-pc'; // SPで読み込まれるlayoutはbodyのclassが固定なので影響が無い
$this->params['breadcrumbsHomeLink'] = ['label' => Yii::t('app', '全国トップ'), 'url' => Url::to('/top/zenkoku')];

$this->params['h1'] = true;

// キープ件数表示
$this->params['keep'] = true;

BootstrapPluginAsset::register($this);
AutoHeightAsset::register($this);

if (!$isMobile) {
    $detailSearch = <<<JS
  $(function($){
    $('.widget.widget2 .widget-data').autoHeight({column:4,clear:1});
    $('.widget.hotJobLayout .widget-data').autoHeight({column:4,clear:1});
  });
JS;
} else {
    /**
     * 注目情報(hotJob)用のレイアウト設定
     * .widget2をhotJobに使用すると
     * widgetの設定が影響するため.hotJobLayoutで対応
     */
    $detailSearch = <<<JS
  $(function($){
    $('.widget.widget2 .widget-data').autoHeight({column:4,clear:1});
  });
JS;
}
$this->registerJs($detailSearch, View::POS_END);

// スマホの場合はtop検索用のオーバーレイを読み込み
if ($isMobile) {
    // 都道府県オーバーレイの読み込み
    echo $this->render('search-top/_pref-overlay', [
        'searchForm' => $searchForm,
    ]);
    // 優先キーが有効な場合は優先キーのオーバーレイを読み込み
    if ($searchForm->principalKey) {
        echo $this->render('@app/views/kyujin/sp/search/_principal-overlay', [
            'searchForm' => $searchForm,
        ]);
    }
} ?>

<div class="container">
    <div class="row">
        <?php if (isset($mainVisual) && $mainVisual->hasActive()): ?>
            <?= $this->render('widget/_main-visual', ['mainVisual' => $mainVisual]); ?>
        <?php endif; ?>
        <!--▼widgetLayout1▼-->
        <?php if (isset($widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_1])): ?>
            <div class="widgetlayout widgetlayout1">
                <?php
                foreach ((array)$widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_1]->widget as $widget) {
                    echo $this->render('widget/_widget', ['widget' => $widget]);
                }

                // 全国トップの場合はエリアトップへのリンクを表示
                echo $this->render('widget/_widget-area', [
                    'searchForm' => $searchForm,
                    'areaId' => $areaId,
                ]);
                ?>
            </div>
        <?php endif; ?>
        <!--▲widgetLayout1▲-->

        <div class="container flexcontainer">
            <div class="row">

                <!--▼widgetLayout2▼-->
                <div class="widgetlayout2 col-sm-8">
                    <!--▼検索フォーム▼-->
                    <?php if (!is_null($areaName) || count($searchForm->areas) <= 1): ?>
                        <div class="widgetlayout">
                            <div class="widget widget-primary">
                                <?php if ($isMobile): ?>
                                    <?= $this->render('search-top/_form-sp', [
                                        'searchForm' => $searchForm,
                                        'areaId' => $areaId,
                                    ]); ?>
                                <?php else: ?>
                                    <?= $this->render('search-top/_form', [
                                        'searchForm' => $searchForm,
                                        'allCount' => $allCount,
                                    ]); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!--▲検索フォーム▲-->

                    <!--▼widgetLayout2-1▼-->
                    <?php if (isset($widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_2])): ?>
                        <div class="widgetlayout widgetlayout2-1">
                            <?php foreach ((array)$widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_2]->widget as $widget) {
                                echo $this->render('widget/_widget', ['widget' => $widget]);
                            } ?>
                        </div>
                    <?php endif; ?>
                    <!--▲widgetLayout2-1▲-->

                    <div class="row">

                        <!--▼widgetLayout2-2▼-->
                        <?php if (isset($widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_3])): ?>
                            <div class="widgetlayout widgetlayout2-2 col-sm-6">
                                <?php foreach ((array)$widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_3]->widget as $widget) {
                                    echo $this->render('widget/_widget', ['widget' => $widget]);
                                } ?>
                            </div>
                        <?php endif; ?>
                        <!--▲widgetLayout2-2▲-->

                        <!--▼widgetLayout2-3▼-->
                        <?php if (isset($widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_4])): ?>
                            <div class="widgetlayout widgetlayout2-3 col-sm-6">
                                <?php foreach ((array)$widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_4]->widget as $widget) {
                                    echo $this->render('widget/_widget', ['widget' => $widget]);
                                } ?>
                            </div>
                        <?php endif; ?>
                        <!--▲widgetLayout2-3▲-->

                    </div>

                    <!--▼widgetLayout2-4▼-->
                    <?php if (isset($widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_5])): ?>
                        <div class="widgetlayout widgetlayout2-4">
                            <?php foreach ((array)$widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_5]->widget as $widget) {
                                echo $this->render('widget/_widget', ['widget' => $widget]);
                            } ?>
                        </div>
                    <?php endif; ?>
                    <!--▲widgetLayout2-4▲-->

                    <!-- 「注目情報表示」 -->
                    <?php if ($hotJob->valid_chk): ?>
                        <?php
                        echo $this->render('hot-job/_hot-job', [
                            'dataProvider' => $dataProvider,
                            'hotJob' => $hotJob,
                        ]);
                        ?>
                    <?php endif; ?>

                </div>
                <!--▲widgetLayout2▲-->

                <!--▼widgetLayout3▼-->
                <?php if (isset($widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_6])): ?>
                    <div class="widgetlayout widgetlayout3 col-sm-4">
                        <?php
                        foreach ((array)$widgetLayouts[WidgetLayout::WIDGET_LAYOUT_NO_6]->widget as $widget):
                            echo $this->render('widget/_widget', ['widget' => $widget]);
                        endforeach;
                        ?>
                    </div>
                <?php endif; ?>
                <!--▲widgetLayout3▲-->
            </div>
        </div>


        <!-- page_top -->
        <div class="page_top">
            <a class="mod-btn1" href="#">▲</a>
            <!-- / .page_top --></div>
    </div>
</div>