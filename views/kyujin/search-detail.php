<?php

use app\models\forms\JobSearchForm;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap\BootstrapPluginAsset;

BootstrapPluginAsset::register($this);

/* @var $this View */
/* @var $searchForm JobSearchForm */
/* @var $allCount integer */
?>
<?= $this->render('search-form/_area-modal', [
    'searchForm'      => $searchForm,
    'allCount'        => $allCount,
    'identifier'      => 'pref_dist_master',
    'otherIdentifier' => 'station',
    'hierarchyFirst'  => 'pref',
    'hierarchySecond' => 'pref_dist_master_parent',
    'hierarchyThird'  => 'pref_dist_master',
]); ?>

<?php if ($searchForm->hasStationKey()): ?>
<?= $this->render('search-form/_station-modal', [
    'searchForm'      => $searchForm,
    'allCount'        => $allCount,
    'identifier'      => 'station',
    'otherIdentifier' => 'pref_dist_master',
    'hierarchyFirst'  => 'station_parent',
    'hierarchySecond' => 'station',
]); ?>
<?php endif; ?>

<?php if ($searchForm->principalKey): ?>
    <?= $this->render('search-form/_principal-modal', [
        'searchForm'      => $searchForm,
        'allCount'        => $allCount,
        'identifier'      => 'principal',
        'hierarchyFirst'  => 'principal_parent',
        'hierarchySecond' => 'principal',
    ]); ?>
<?php endif; ?>

<?= Html::beginForm('search-result', 'post', [
    'id' => 'search-form',
]); ?>

<div class="container subcontainer flexcontainer">
    <h1 class="resultTitle"><?= Yii::t('app', '詳細検索'); ?></h1>
    <div class="mod-resultForm">
        <?= $this->render('@app/views/common/_search-box', [
            'searchForm' => $searchForm,
            'allCount'   => $allCount,
        ]); ?>
    </div>
</div>
<div class="s-fix-footer-block">
    <div class="fix-footer">
        <?= Html::a(
            Html::tag(
                'span',
                null,
                [
                    'class' => [
                        'fa',
                        'fa-search',
                    ],
                ]
            ) . Html::encode(Yii::t('app', '検索')) .
            Html::tag(
                'span',
                Html::encode($allCount !== null ? "({$allCount})" : '(0)'),
                [
                    'class' => [
                        'outer-num',
                    ],
                ]
            ),
            null,
            [
                'type'  => 'button',
                'class' => [
                    'form-submit',
                    'mod-btn7',
                    'resultForm__button',
                ],
            ]
        ); ?>
    </div>
</div>
<?= Html::endForm(); ?>
