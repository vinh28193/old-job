<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use proseeds\assets\AdminAsset;

AdminAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>

    <title>JobMaker管理画面-<?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body class="drawer drawer-left drawer-responsive">
<?php $this->beginBody() ?>
<div id="wrapper">
    <!-- sidebar =============================================== -->
    <?= $this->render('_side-menu') ?>
    <div class="drawer-overlay">
        <!-- navi ================================================== -->
        <?= $this->render('_nav-bar') ?>
        <!-- main ================================================== -->
        <div id="main">
            <div id="scroller">
                <?php if (isset($this->params['breadcrumbs'])) {
                    echo Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]);
                } else {
                    echo Html::tag('ul','',['class' => 'breadcrumb']);
                } ?>
                <div id="main_in"><?= $content ?>
                    <p class="copy cf">powered by willb</p>
                </div>
            </div>
        </div>
        <!-- /main ================================================= -->
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
