<?php
/* @var $this \yii\web\View */
/* @var $content string */
/* @var $socialButtonList array */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\MainAsset;
use app\common\Helper\JmUtils;
use app\models\manage\SiteHtml;
use yii\helpers\Url;

MainAsset::register($this);

/** @var \app\common\Site $site */
$site = Yii::$app->site;
$title = (!JmUtils::isEmpty($this->title) ? $this->title . Yii::t('app', '｜') : '') . $site->toolMaster->title;
/* @var $siteHtml SiteHtml */
$siteHtml = $this->params['siteHtml'];

// todo メタタグはViewの$metaTags使うように修正
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="keyword" content="<?= Html::encode($this->params['keyword'] ?? $site->toolMaster->keywords); ?>">
    <meta name="description" content="<?= Html::encode($this->params['description'] ?? $site->toolMaster->description); ?>">

    <?php if (isset($socialButtonList)): ?>
        <?php foreach ((array)$socialButtonList as $socialButton): ?>
            <?php /* @var $socialButton \app\models\manage\SocialButton */ ?>
            <?= $socialButton->social_meta ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <title><?= Html::encode($title) ?></title>

    <!--if lt IE 9
    script(src='https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js')
    script(src='https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js')
    -->

    <?php $this->head() ?>

<!-- アナリティクスタグ -->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    <?php if (isset($this->params['isAnalytics'])): ?>
        ga('create', '<?php echo Yii::$app->googleAnalytics->trackingCode ?>', 'auto', {'name': 'access_log'});
        ga('access_log.set', 'dimension1', '<?php echo time(); ?>');
        ga('access_log.set', 'dimension2', '<?php echo Url::toRoute($_SERVER['REQUEST_URI'], true); ?>');
        ga('access_log.set', 'dimension3', '<?php echo $_SERVER['HTTP_USER_AGENT']; ?>');
        ga('access_log.set', 'dimension4', '<?php echo $this->params['analyticsParam']; ?>');
        ga('access_log.set', 'dimension5', '<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '(direct)'; ?>');
        ga('access_log.set', 'dimension6', '<?php echo time(); ?>,<?php echo Url::toRoute($_SERVER['REQUEST_URI'], true); ?>,<?php echo $this->params['analyticsParam']; ?>');
        ga('access_log.send', 'pageview');
    <?php endif; ?>
    <?= $siteHtml->analytics_html  ?>
</script>
<!-- アナリティクスタグ -->

    <?= $siteHtml->another_html  ?>

    <?php // サイトごとのカスタムのCSSを追加する。
    echo Html::cssFile(Url::to([JmUtils::fileUrl('css/custom.css'), 'public' => true]), ['type' => 'text/css']);
    ?>
</head>

<?= Html::beginTag('body', ['class' => ArrayHelper::getValue($this->params, 'bodyClass', 'body'), 'id' => ArrayHelper::getValue($this->params, 'bodyId')]) ?>
<?php $this->beginBody() ?>

<?php if (ArrayHelper::getValue($this->params, 'h1', false) == true): ?>
    <div class="pagetitle">
        <div class="container">
            <h1><?= Html::encode($site->toolMaster->h1); ?></h1>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($this->params['overHeader'])): ?>
    <?= $this->params['overHeader'];; ?>
<?php endif; ?>

<!-- header =================================================== -->
<header class="header">
    <?= $siteHtml->header_html; ?>
</header>
<!-- /header =================================================== -->


<?php if (ArrayHelper::getValue($this->params, 'keep', false)): // keepボタン?>
    <?= $this->render('@app/views/keep/_keep-count.php'); ?>
<?php endif; ?>

<?php if (Yii::$app->requestedRoute != 'top/index' || Yii::$app->request->queryParams): // パンくず ?>
    <?= Html::tag('div', Breadcrumbs::widget([
        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        'options' => ['class' => 'container'],
        'homeLink' => ArrayHelper::getValue($this->params, 'breadcrumbsHomeLink', null),
    ]), ['class' => 'breadcrumb']) ?>
<?php endif; ?>

<?= $content ?>
<?= $siteHtml->footer_html; ?>

<?php if (isset($this->params['requiredItemNumBox'])): ?>
    <div class="mod-requiredItemNumBox text-center">
        <?= Yii::t('app', '入力が必要な項目は、残り') ?><span class="requiredItemNum"></span><?= Yii::t('app', '件です。') ?>
    </div>
<?php endif; ?>

<?php $this->endBody(); ?>

<?= $siteHtml->remarketing_html  ?>

<?php
if (strrpos(Yii::$app->request->url, '/apply/complete') !== false): // 応募コンバージョンタグ
    echo $siteHtml->conversion_html;
endif;
?>

<?= Html::endTag('body') ?>
</html>
<?php $this->endPage(); ?>
