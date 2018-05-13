<?php
/* @var $this \yii\web\View */

use yii\helpers\Html;

?>

<!-- header =================================================== -->
<header class="header">
    <div class="container">
        <!-- Smart Phone Navigation Button ======= -->
        <button type="button" class="navbar-toggle offcanvas-toggle" data-toggle="offcanvas" data-target="#header-nav">
            <span class="sr-only">Menu</span>
        </button>
        <!-- /Smart Phone Navigation Button ======= -->
        <!--<h1 class="pagetitle">ＷＥＢデザイン・編集系インターン（求人ID：00000）｜ JobMaker2</h1>-->
        <div class="logo"><a href="/"><?= Html::img('/pict/logo.png', ['width' => 200, 'height' => 50]) ?></a></div>

        <!-- Nav ==================== -->
        <nav class="navbar navbar-offcanvas navbar-offcanvas-right navbar-offcanvas-touch navbar-offcanvas-fade" role="navigation" id="header-nav">
            <!-- close btn -->
            <button type="button" class="navbar-toggle offcanvas-toggle pull-right" data-toggle="offcanvas" data-target="#header-nav">
                <span class="sr-only">Close</span>
                <span class="glyphicon glyphicon-remove"></span>
            </button>
            <!-- Navigation -->
            <div class="nav-wrapper nav navbar-nav">
                <ul class="nav--sub nav navbar-nav hide-sp">
                    <li><a href="mailto:jobmaker@pro-seeds.com"><span class="fa fa-envelope"></span> お問い合わせ</a></li>
                    <li class="nav__phone"><a href="#"><span class="fa fa-phone"></span> 000-0000-0000</a></li>
                </ul>
                <ul class="nav--main nav navbar-nav">
                </ul>
                <!--mobile menu -->
                <ul class="nav--sub nav navbar-nav only-sp">
                    <li><a href="mailto:jobmaker@pro-seeds.com"><span class="fa fa-envelope"></span> お問い合わせ</a></li>
                    <li class="nav__phone">お電話でのお問い合わせ<a href="tel:000-0000-0000" class="mod-btn3"><span class="fa fa-phone"></span> 000-0000-0000</a></li>
                </ul>
            </div>
        </nav>
    </div><!-- / .container -->
</header>
<!-- /header =================================================== -->