<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/03/23
 * Time: 14:56
 */
use app\models\forms\JobSearchForm;
use yii\helpers\Html;

/* @var JobSearchForm $searchForm */
?>
<div class="s-overlay-wrap-block js-overlay" id="ovl-area-single">
    <div class="c-title-label op-thin">
        <div class="left">
            <h2 class="title"><?= Yii::t('app', '都道府県で探す') ?></h2>
        </div>
        <div class="right">
            <ul class="c-btn-list">
                <li><a class="c-btn js-overlay-trigger-off" href="#"><?= Yii::t('app', '戻る') ?></a></li>
            </ul>
        </div>
    </div>
    <div class="c-page-head-lead-message-block">
        <div class="page-head-lead-message">
            <?= Yii::t('app', '働きたい都道府県を選択してください。') ?>
        </div>
    </div>
    <div class="c-overlay-content-wrap-block">
        <div class="s-overlay-select-category-block">
            <div class="s-btn-radio-wrap-block c-select-area">
                <ul class="c-btn-radio-wrap">
                    <?php foreach ($searchForm->prefs as $prefNo => $pref): ?>
                        <!--ここからループ-->
                        <li class="list c-btn-radio pref js-overlay-trigger-off" data-target="hidden-pref_string">
                            <input value="<?= $pref->pref_no ?>" name="prefRadio" type="radio" id="button<?= $prefNo ?>">
                            <label for="button<?= $prefNo ?>"><?= Html::encode($pref->pref_name) ?></label>
                        </li>
                    <?php endforeach; ?>
                    <!--ここまでループ-->
                </ul>
            </div>
        </div>
    </div>
</div>