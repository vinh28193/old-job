<?php

use app\common\KyujinForm;
use yii\helpers\Html;

/**
 * @var $this    \yii\web\View
 * @var $inquiry \app\models\manage\InquiryMaster
 */

$this->title                   = Yii::t('app', '掲載のお問い合わせ確認');
$this->params['breadcrumbs'][] = ['label' => '掲載のお問い合わせ', 'url' => ['inquiry/index']];
$this->params['breadcrumbs'][] = $this->title;
$currentClass['confirm']       = true;
$this->params['bodyId']        = 'inquiry-confirm';

$buttonJs = <<<JS
    $('button[type=button].submit').click(function () {
        $(this).prop("disabled", true);
        var form = $(this).parents('form');
        form.attr('action', $(this).data('action'));
        form.submit();
        var that = this;
        setTimeout(function() {
            $(that).prop("disabled", false);
        }, 10000);
    });
JS;
$this->registerJs($buttonJs);

// no index metaタグを追加
$this->registerMetaTag([
    'name' => 'robots',
    'content' => 'noindex',
]);
?>
<div class="container subcontainer">
    <div class="row">
        <!--▼ここからコンテンツスタート▼-->
        <div class="col-sm-12">

            <?= $this->render('_flow', ['currentClass' => $currentClass]) ?>

            <h2 class="mod-h4"><?= Yii::t('app', '以下の内容でお間違えなければ「お問い合わせする」ボタンを押してください。') ?></h2>

            <?php
            $kyujinForm = KyujinForm::begin(['id' => 'inquiry-confirm-form', 'enableClientValidation' => false]);
            $kyujinForm->beginTable();
            foreach (Yii::$app->functionItemSet->inquiry->items as $attribute => $inquiryColumnSet) {
                /* @var $inquiryColumnSet \app\models\manage\InquiryColumnSet */
                echo $kyujinForm->row($inquiry, $attribute)->textWithHiddenInput();
            }
            $kyujinForm->endTable();
            ?>
            <div class="mod-box-center w90">
                <?= Html::button(Yii::t('app', '戻る'),
                    ['type' => 'button', 'class' => 'mod-btn3 w40 submit', 'name' => 'act', 'data-action' => '/inquiry/index']) ?>
                <?= Html::button(Yii::t('app', 'お問い合わせする'),
                    ['type' => 'button', 'class' => 'mod-btn2 w55 submit', 'name' => 'act', 'data-action' => '/inquiry/register']) ?>
            </div>
            <?php $kyujinForm->end(); ?>
            <!--▼ここでコンテンツエンド▼-->
        </div><!-- / .col-sm-12 -->
    </div>
</div>
