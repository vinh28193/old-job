<?php
use app\modules\manage\models\Manager;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/** @var Manager $identity */
$identity = Yii::$app->user->identity;
?>

<?php
NavBar::begin([
    'options' => [
        'class' => 'navbar navbar-default mainnav',
    ],
    //'brandLabel' => "<img src='http://job-maker.jp/pict/logo.png' height='50px'>",    //ブランドアイコン
    //'brandUrl' => Yii::$app->homeUrl,                                                 //ブランドアイコンのURL
    //'brandOptions' => ['class' => 'navbar-header'],                                   //ブランドアイコンのaタグの属性

    'containerOptions' => ['class' => 'collapse navbar-collapse', 'id' => 'bs-example-navbar-collapse-1'],
    'innerContainerOptions' => ['class' => 'container-fluid'] //navタグの子のdivタグの属性
]);
?>

<?php
$adminItems = [
    '<li><a href=' . Url::toRoute('secure/admin/profile') . '> ' . Yii::t('app', '&gt;マイプロフィール編集') . '</a></li>',
];
$adminNavItems = ArrayHelper::merge($adminItems, [
    '<li class="divider"></li>',
    '<li><a href=""><span class="glyphicon glyphicon-book"></span>' . Yii::t('app', 'ヘルプ-Coming Soon!') . '</a></li>',
    '<li><a href="mailto:pro-jm@pro-seeds.com"><span class="glyphicon glyphicon-envelope"></span>' . Yii::t('app', 'お問合せ') . '</a></li>',
    '<li><a href="/manage/logout"><span class="glyphicon glyphicon-log-out"></span>' . Yii::t('app', 'ログアウト') . '</a></li>',
]);

echo Nav::widget([
    'options' => ['class' => 'nav navbar-nav navbar-right'],
    'encodeLabels' => false,
    'dropDownCaret' => '',
    'items' => [//todo 今はべた書きだがデータベースや権限から動的に生成
        [
            'label' => '<span class="glyphicon glyphicon-chevron-right"></span>' . Yii::t('app', '求職者画面'),
            'url' => '/',
            'options' => ['class' => 'dropdown no-mobile'],
            'linkOptions' => ['target' => '_blank'],
        ],
        /*[
            'label' => '<span class="glyphicon glyphicon-bell"></span><span class="no-mobile">お知らせ</span><span class="badge">5</span>',
            'items' => [
                '<li class="dropdown-header"><h5>Coming Soon!</h5></li>',
                '<li><a href="">03/25<br>○○○○○○○○○○機能を追加しました。</a></li>',
                '<li><a href="">03/25<br>○○○○○○○○○○機能を追加しました。</a></li>',
                '<li class="divider"></li>',
                '<li><a href="">もっと見る</a></li>',
            ],
        ],*/
        [
            'label' => '<span class="glyphicon glyphicon-user"></span><span class="no-mobile">' . Html::encode($identity->name_sei . ' ' . $identity->name_mei) . '</span><b class="caret"></b>',
            'items' => $adminNavItems,
        ],
        /*[
            'label' => '<span class="glyphicon glyphicon-globe"></span><b class="caret"></b>',
            'items' => [
                '<li><a href="http://job-maker.jp/manage/secure/#">日本語（Japanese）</a></li>',
                '<li class="divider"></li>',
                '<li style="padding:5px 16px">英語（English）-contact us!</li>',
                '<li style="padding:5px 16px">中国語（chinese) -contact us!</li>',
            ],
        ],*/
    ],
]);
NavBar::end();
?>
