<?php

use app\common\Keep;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;

/* @var $this View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->params['keep'] = true;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'キープ一覧'), 'url' => Url::to()];

$cautionHtml1 = Yii::t('app', '※他のPC/スマートフォンで登録した求人情報は、ご利用いただけません。');
$cautionHtml2 = Yii::t('app', '※掲載が終了した求人情報は表示されなくなります。');
$cautionHtml3 = Yii::t('app', '※登録できる求人件数は{KEEP_LIMIT}件までです。', ['KEEP_LIMIT' => Keep::KEEP_LIMIT]);
$cautionHtml4 = Yii::t('app', '※キープした求人情報の保存期間は30日間です。');

// no index metaタグを追加
$this->registerMetaTag([
    'name' => 'robots',
    'content' => 'noindex',
]);
?>
<div class="container subcontainer flexcontainer">
    <div class="row">

        <?php if ($dataProvider == null): ?>
            <!--▼ここからコンテンツスタート▼-->
            <div class="col-sm-12">

                <h1 class="resultTitle"><?= Yii::t('app', 'キープ一覧'); ?></h1>

                <div class="mod-subbox-wrap">
                    <div class="mod-subbox">
                        <h1 class="mod-subbox" style="margin-top: 0;"><?= Yii::t('app', 'キープした求人情報はありません。'); ?></h1>

                        <p class=""><?= Yii::t('app', '検索結果一覧や求人詳細の「キープ」ボタンから気になる求人情報を保存することができます。'); ?></p>

                        <p class="">
                            <?= $cautionHtml1; ?><br>
                            <?= $cautionHtml2; ?><br>
                            <?= $cautionHtml3; ?><br>
                            <?= $cautionHtml4; ?>
                        </p>

                        <div class="mod-box-center w60 w90-sp">
                            <p class="mod-btn2"><a href="<?= Url::to('/') ?>"><?= Yii::t("app", 'トップページへ戻る') ?></a></p>
                        </div>
                    </div><!-- / .mod-subbox -->
                </div><!-- / .mod-subbox-wrap -->

            </div>
            <!--▲ここでコンテンツエンド▲-->
        <?php else: ?>
            <div id="result_container" style="min-height: 600px;">
                <!--▼ここからコンテンツスタート▼-->

                <div class="col-sm-12">

                    <h1 class="resultTitle"><?= Yii::t('app', 'キープ一覧'); ?></h1>

                    <div class="mod-subbox-wrap">
                        <div class="mod-subbox">
                            <p class="">
                                <?= $cautionHtml1; ?><br>
                                <?= $cautionHtml2; ?><br>
                                <?= $cautionHtml3; ?><br>
                                <?= $cautionHtml4; ?>
                            </p>
                        </div><!-- / .mod-subbox -->
                    </div><!-- / .mod-subbox-wrap -->


                    <h2 class="mod-subbox mod-h3" style="margin-top: 0;padding-top: 0;">
                        <?= Yii::t('app', 'キープした求人情報は{totalCount}件です。', [
                            'totalCount' => Html::tag('span', $dataProvider->totalCount, ['class' => 'keep-num', 'style' => 'font-size: 24px;color:#4dac26;']),
                        ]); ?>
                    </h2>
                    <!--▼求人の一覧ボックス▼-->
                    <div class="col-sm-12">
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemView' => '../kyujin/_job_disp_itemview',
                            'summary' => '',
                            'itemOptions' => ['class' => 'mod-jobResultBox-wrap'],
                        ]); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
