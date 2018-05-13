<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/03/21
 * Time: 18:03
 */

use app\models\forms\JobSearchForm;
use app\common\Helper\Html;

/** @var array $conditions */
/* @var $searchForm JobSearchForm */
// スマホ優先キーの親の名前
$principalChild = $searchForm->principalKey->table_name ?? '';
// スマホ優先キーの子の名前
$principalParent = $principalChild . '_parent';

?>
<?= Html::beginForm('/kyujin/search-detail', 'POST', ['name' => 'form']) ?>
    <div class="s-fix-header-block">
        <div class="fix-header">
            <div class="s-header-search-block">
                <div class="header-search">
                    <div class="header-search-title"><?= Yii::t('app', '現在の検索条件') ?></div>
                    <ul class="header-search-item js-omit-select-txt-block">
                        <?php foreach ($conditions as $attribute => $condition): ?>
                            <?php if ($attribute == 'area'): ?>
                                <?= Html::hiddenInput($attribute, $condition) ?>
                            <?php elseif ($attribute == 'keyword'): ?>
                                <?= Html::tag(
                                    'li',
                                    Html::tag('h1', $condition),
                                    ['class' => 'js-omit-select-txt-item']
                                ) ?>
                                <?= Html::hiddenInput($attribute, $condition) ?>
                            <?php else: ?>
                                <?php if (in_array($attribute, [
                                    'pref',
                                    'pref_dist_master_parent',
                                    'pref_dist_master',
                                    'station_parent',
                                    'station',
                                ])): ?>
                                    <?= Html::hiddenInput($attribute . '_string', implode(',', $condition)); ?>
                                <?php elseif ($attribute == $principalParent): ?>
                                    <?= Html::hiddenInput('principal_parent_string', implode(',', $condition)); ?>
                                <?php elseif ($attribute == $principalChild): ?>
                                    <?= Html::hiddenInput('principal_string', implode(',', $condition)); ?>
                                <?php endif; ?>

                                <?php foreach ($condition as $name => $no): ?>
                                    <?php if ($attribute == 'wage_category' || $attribute == 'wage_category_parent'): ?>
                                        <?= Html::hiddenInput($attribute, $no) ?>
                                    <?php else: ?>
                                        <?= Html::hiddenInput($attribute . '[]', $no) ?>
                                    <?php endif; ?>
                                    <?= Html::tag(
                                        'li',
                                        Html::tag('h1', $name),
                                        ['class' => 'js-omit-select-txt-item']
                                    ) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <div class="header-search-change">
                        <?= Html::a(Yii::t('app', '検索条件を変更'), 'javascript:document.form.submit();', ['class' => 'change-btn']) ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?= Html::endForm() ?>