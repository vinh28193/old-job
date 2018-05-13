<?php

use app\assets\ApplyAsset;
use app\common\KyujinForm;
use app\models\Apply;
use \app\models\JobMasterDisp;
use app\models\manage\ApplicationColumnSet;
use app\models\manage\ApplicationMaster;
use yii\helpers\Html;
use app\models\manage\NameMaster;
use yii\web\View;
use app\models\ToolMaster;

/* @var $this View */
/* @var $applicationModel ApplicationMaster */
/* @var $jobMaster JobMasterDisp */
/* @var $jobDispList array */
/* @var $apply Apply */


ApplyAsset::register($this);

Yii::$app->site->toolNo = ToolMaster::TOOLNO_MAP['applicationConfirmation'];
Yii::$app->site->jobMaster = $jobMaster;

// 表示項目取得
/* @var $items ApplicationColumnSet[] */
$items = Yii::$app->functionItemSet->application->applyDispItems;
// 郵便番号入力表示フラグ
$postalCodeFlg = isset($items['address']);

$title = Yii::t('app', '{corpNameDisp}{applicationName}確認',
    ['corpNameDisp' => $jobMaster->corp_name_disp, 'applicationName' => NameMaster::getChangeName('応募')]);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', '{corpNameDisp}の求人詳細', ['corpNameDisp' => $jobMaster->corp_name_disp]),
    'url' => ['/kyujin/index', 'job_no' => $jobMaster->job_no],
];
$this->params['breadcrumbs'][] = $title;
$currentClass['confirm'] = true;
$this->params['bodyId'] = 'apply-confirm';

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
$this->params['h1'] = true;

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

            <?= $this->render('/common/_job-short-item-disp', ['model' => $jobMaster, 'headerMessage' => Yii::t('app', '応募先情報')]) ?>

            <h2 class="mod-h4"><?= Yii::t('app', '以下の内容でお間違えなければ「応募する」ボタンを押してください。') ?></h2>

            <?php // フォーム
            $kyujinForm = KyujinForm::begin(['id' => 'apply-confirm-form', 'enableClientValidation' => false]);
            $kyujinForm->beginTable();

            foreach ($items as $attribute => $item) {
                if ($attribute === 'birth_date') {
                    echo $kyujinForm->row($apply, $attribute)->textWithHiddenInput([], 'dateFormatter');
                } elseif ($attribute == 'pref_id' || $attribute == 'address') {
                    // 都道府県と住所の場合、まだ郵便番号が表示されていないなら表示する
                    if ($postalCodeFlg) {
                        $postalCodeFlg = false;
                        echo $kyujinForm->row($apply, 'postalCode')->textWithHiddenInput();
                    }
                    echo $kyujinForm->row($apply, $attribute)->textWithHiddenInput();
                } else {
                    echo $kyujinForm->row($apply, $attribute)->textWithHiddenInput();
                }
            }

            $kyujinForm->endTable();
            ?>

            <?= Html::hiddenInput('job_no', $jobMaster->job_no) ?>
            <?= Html::activeHiddenInput($apply, 'job_master_id', ['value' => $jobMaster->id]) ?>

            <div class="mod-box-center w90">
                <?= Html::button(Yii::t('app', '戻る'), [
                    'type' => 'button',
                    'class' => 'mod-btn3 w40 submit',
                    'name' => 'act',
                    'data-action' => '/apply/' . $jobMaster->job_no,
                ]) ?>
                <?= Html::button(Yii::t('app', '応募する'),
                    ['type' => 'button', 'class' => 'mod-btn2 w55 submit', 'name' => 'act', 'data-action' => '/apply/register']) ?>
            </div>
            <?php $kyujinForm->end(); ?>
            <!--▼ここでコンテンツエンド▼-->
        </div><!-- / .col-sm-12 -->
    </div>
</div>
