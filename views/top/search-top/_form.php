<?php
use app\models\forms\JobSearchForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $searchForm JobSearchForm */
/* @var $form \yii\widgets\ActiveForm */
/* @var $allCount integer */

$topSearch = <<<JS
$('#search-form-submit').on('click', function(){
    if($('#station').val()){
        $('#station_parent').val('');
    }
    $('#search-form').submit();
    return false;
});
JS;
$this->registerJs($topSearch, View::POS_END);
?>

<?= $this->render('@app/views/kyujin/search-form/_area-modal', [
    'searchForm'      => $searchForm,
    'identifier'      => 'pref_dist_master',
    'otherIdentifier' => 'station',
    'hierarchyFirst'  => 'pref',
    'hierarchySecond' => 'pref_dist_master_parent',
    'hierarchyThird'  => 'pref_dist_master',
]); ?>

<?php if ($searchForm->hasStationKey()): ?>
<?= $this->render('@app/views/kyujin/search-form/_station-modal', [
    'searchForm'      => $searchForm,
    'identifier'      => 'station',
    'otherIdentifier' => 'pref_dist_master',
    'hierarchyFirst'  => 'station_parent',
    'hierarchySecond' => 'station',
]); ?>
<?php endif; ?>

<?php if ($searchForm->principalKey): ?>
    <?= $this->render('@app/views/kyujin/search-form/_principal-modal', [
        'searchForm'      => $searchForm,
        'identifier'      => 'principal',
        'hierarchyFirst'  => 'principal_parent',
        'hierarchySecond' => 'principal',
    ]); ?>
<?php endif; ?>

<h2 class="mod-h7"><?= Yii::t('app', '求人検索') ?></h2>

<?= Html::beginForm('search-result', 'post', [
    'id'     => 'search-form',
    'action' => 'search-result',
    'class'  => [
        'mod-resultForm',
        'op-border-none',
    ],
]); ?>
<?= $this->render('@app/views/common/_search-box', [
    'searchForm' => $searchForm,
]); ?>

<?= Html::endForm(); ?>

<div class="button-group op-wiget">
        <?= Html::a(
            Yii::t('app', '条件をもっと細かく設定') . '</a>',
            null,
            [
                'id'    => 'search-to-detail',
                'class' => [
                    'form-button',
                    'search-to-detail',
                    'collapse-button',
                    'mod-btn8',
                ],
                'data'  => [
                    'action' => '/kyujin/search-detail',
                ],
            ]
        ) ?>
    <?= Html::a(
        '<span class="fa fa-search"></span>' .
        Yii::t('app', 'この条件でお仕事をさがす') .
        Html::tag(
            'span',
            Html::encode(isset($allCount) ? "({$allCount})" : ''),
            [
                'class' => [
                    'outer-num',
                ],
            ]
        ),
        null,
        [
            'id'    => 'search-form-submit',
            'class' => [
                'form-submit',
                'form-button',
                'mod-btn7',
            ],
        ]
    ) ?>
</div>
