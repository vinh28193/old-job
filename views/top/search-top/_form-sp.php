<?php
/**
 * top画面検索モジュール
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var JobSearchForm $searchForm */
/* @var $areaId int */
$searchFormId  = 'searchForm';
$keywordFormId = 'keywordForm';

$initJs = <<<JS
    function appendSelectedItems() {
        $('.c-btn-radio-item').remove();
        var t =$('.list-item.principal').children();
        $('.list-item.principal').children().each(function () {
            var label = $("<span></span>", {
                'class': 'c-btn-radio-item ' + $(this).attr('class')
            }).text($(this).text());
            $('.input-selexted-box.js-omit-select-btn').append(label);
        });
    }
    appendSelectedItems();
JS;
$this->registerJs($initJs)

?>
<!--▼都道府県・職域検索▼-->
<?= Html::beginForm('/kyujin/search-result', 'post', ['id' => $searchFormId]); ?>
<?= Html::hiddenInput('area', $areaId) ?>

<?= Html::hiddenInput('pref_string', null) ?>

<?= Html::hiddenInput('principal_parent_string', null, ['id' => 'hidden-principal_parent']); ?>
<?= Html::hiddenInput('principal_string', null, ['id' => 'hidden-principal']); ?>

<div class="s-search-easy-home-block">
    <?php if ($searchForm->hasPrefKey() || $searchForm->principalKey): ?>
    <div class="search-easy-select-block">
        <div class="search-easy-title-block">
            <div class="search-easy-title">
                <?= Yii::t('app', '簡単に仕事を探す') ?>
            </div>
            <div class="search-item-free-word-box js-show-toggle">
                <div class="s-search-select-free-word-block">
                    <div class="left">
                        <div class="s-input-selexted-block">
                            <div class="input-selexted-box js-omit-select-btn">
                            </div>
                        </div>
                    </div>
                    <div class="right">
                        <?= Html::submitButton(Yii::t('app', '検索'), ['class' => 'c-input-btn-submit']) ?>
                    </div>
                </div>
            </div>

            <div class="s-select-area-block js-selected-category">
                <div class="select-area">
                    <div class="s-btn-wrap-block">
                        <ul class="row" style="margin: 0;padding: 0;list-style: none;">
                            <?php if ($searchForm->hasPrefKey()): ?>
                                <li class="col-xs-6">
                                    <?= Html::a(Yii::t('app', '{area}を選ぶ', ['area' => $searchForm->searchKeys['pref']->searchkey_name]) . '<i class="fa fa-chevron-right"></i>', '#ovl-area-single', [
                                        'class' => 'c-btn-radius op-bg-link  op-fz-l js-overlay-trigger-on',
                                    ]) ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($searchForm->principalKey): ?>
                                <li class="col-xs-6">
                                    <?= Html::a(Yii::t('app', '{key}を選ぶ', ['key' => $searchForm->principalKey->searchkey_name]) . '<i class="fa fa-chevron-right"></i>',
                                        '#ovl-job',
                                        ['class' => 'c-btn-radius op-bg-link  op-fz-l js-overlay-trigger-on',]) ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="search-easy-link-block">
        <div class="search-easy-link">
            <?= Html::a(Yii::t('app', '詳細検索をする'), 'javascript:void(0)', [
                'class' => 'js-change-submit',
                'data'  => ['form-selector' => '#' . $searchFormId, 'action' => '/kyujin/search-detail'],
            ]) ?>
        </div>
    </div>
</div>
<?= Html::endForm(); ?>
<!--▲都道府県・職域検索▲-->
<!--▼フリーワード検索▼-->
<?= Html::beginForm('/kyujin/search-result', 'post', ['id' => $keywordFormId]); ?>
<?= Html::hiddenInput('area', $areaId) ?>
<div class="p-home-free-word-block">
    <div class="free-word">
        <div class="free-word-title">
            <?= Yii::t('app', 'フリーワードで検索') ?>
        </div>
    </div>
    <div class="s-job-result-selected-result-search-item-block">
        <div class="search-item-free-word-box">
            <div class="s-search-select-free-word-block">
                <div class="left">
                    <?= Html::textInput('keyword', null, [
                        'class'       => 'c-input-text c-input-placeholder',
                        'placeholder' => Yii::t('app', 'キーワード'),
                    ]) ?>
                </div>
                <div class="right">
                    <?= Html::submitButton(Yii::t('app', '検索'), ['class' => 'c-input-btn-submit']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= Html::endForm(); ?>
<!--▲フリーワード検索▲-->

