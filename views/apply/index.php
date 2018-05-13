<?php

use app\assets\AjaxZip3Asset;
use app\common\KyujinForm;
use app\models\Apply;
use app\models\JobMasterDisp;
use app\models\manage\ApplicationColumnSet;
use app\models\manage\Policy;
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\assets\ApplyAsset;
use app\assets\ConfirmBoxAsset;
use app\models\manage\searchkey\Pref;
use app\models\manage\Occupation;
use app\models\manage\NameMaster;
use app\models\ToolMaster;
use yii\web\View;

/* @var $this View */
/* @var $apply Apply */
/* @var $jobMaster JobMasterDisp */
/* @var $policy Policy */

ApplyAsset::register($this);
ConfirmBoxAsset::transitionConfirmBox($this, Yii::t('app', '入力中のデータが保存されませんが、よろしいですか？'));
TextareaAutosizeAsset::register($this);
AjaxZip3Asset::register($this);

Yii::$app->site->toolNo = ToolMaster::TOOLNO_MAP['applicationInput'];
Yii::$app->site->jobMaster = $jobMaster;

// 表示項目取得
/* @var $items ApplicationColumnSet[] */
$items = Yii::$app->functionItemSet->application->applyDispItems;
// 郵便番号入力表示フラグ
$postalCodeFlg = true;
if (isset($items['address'])) {
    // 住所入力があればプラグインをrenderingする
    if (isset($items['pref_id'])) {
        $prefAttribute = 'pref_id';
    } else {
        $prefAttribute = 'address';
    }
    $js = <<<JS
jQuery("input[name = 'Apply[postalCode]']").keyup(function(e) {
    AjaxZip3.zip2addr(this, '', 'Apply[{$prefAttribute}]', 'Apply[address]');
});
JS;
    $this->registerJs($js);
} else {
    // 住所入力がなければ郵便番号入力は表示しない
    $postalCodeFlg = false;
}

$title = Yii::t('app', '{corpNameDisp}{applicationName}', [
    'corpNameDisp' => $jobMaster->corp_name_disp,
    'applicationName' => NameMaster::getChangeName('応募'),
]);
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', '{corpNameDisp}の求人詳細', ['corpNameDisp' => $jobMaster->corp_name_disp]),
    'url' => ['/kyujin/index', 'job_no' => $jobMaster->job_no],
];
$this->params['breadcrumbs'][] = $title;
$this->params['bodyId'] = 'apply';

$this->params['h1'] = true;

//応募フロー
$currentClass['input'] = 'class="current"';
//残り必須項目表示
$this->params['requiredItemNumBox'] = true;

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

            <h2 class="mod-h4"><?= Yii::t('app', '応募情報をご入力ください') ?></h2>

            <?php // フォーム
            $kyujinForm = KyujinForm::begin(['id' => 'apply-form', 'action' => '/apply/confirm']);
            $kyujinForm->beginTable();


            foreach ($items as $columnName => $item) {
                //メイン項目
                switch ($columnName) {
                    case 'mail_address':
                        //メールアドレス
                        echo $this->render('/common/_complete-mail-domain-form', [
                            'form' => $kyujinForm,
                            'model' => $apply,
                            'columnName' => $columnName,
                            'hintMessage' => Yii::t('app', '※入力されたメールアドレスに応募に関するメールが届きます。'),
                        ]);
                        break;
                    case 'sex':
                        //性別
                        echo $kyujinForm->row($apply, $columnName)
                            ->radioList($apply->formatTable['sex'], ['tag' => 'ul', 'class' => 'mod-form1 inline-radio']);
                        break;
                    case 'birth_date':
                        //生年月日 todo 会員機能のタイミングでこいつもKyujinFieldに入れたい
                        $birthDayPlugin = <<<JS
$('#apply-form').on('beforeValidateAttribute', function(event, attribute, messages) {
  if (attribute.name == "birthDateYear" || attribute.name == 'birthDateMonth' || attribute.name == 'birthDateDay') {
      if ($('#apply-birthdateyear').val() && $('#apply-birthdatemonth').val() && $('#apply-birthdateday').val()) {
        $('#apply-birth_date').val(1);
      } else {
        $('#apply-birth_date').val('');
      }
    $("#apply-form").yiiActiveForm("validateAttribute", 'apply-birth_date');
  }
});

function leapYearCheck(){
  var y = $("#apply-birthdateyear").val();
  var m = $("#apply-birthdatemonth").val();
  var d = $("#apply-birthdateday").val();
  $('#apply-birthdateday').empty();

  if (m === "") {
    var last = 31;
  } else if (m == 2 && (y % 400 == 0 || (y % 4 == 0 && y % 100 != 0))) {
    var last = 29;
  } else {
    var last = new Array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31)[m - 1];
  }
  $('#apply-birthdateday').append('<option value="">----</option>');
  for (var i = 1; i <= last; i++) {
    var v = ("0" + i).slice( -2 );
    $('#apply-birthdateday').append('<option value="' + v + '">' + v + '</option>');
  }
  if (last >= d) {
    $("#apply-birthdateday").val(d)
  }
}

// 年を選択ごとに日付を修正して表示
$('#apply-birthdateyear').change(function(){
  leapYearCheck();
});
// 月を選択ごとに日付を修正して表示
$('#apply-birthdatemonth').change(function(){
  leapYearCheck();
});

$('#apply-birthdateday').focus(function(){
  leapYearCheck();
});
JS;
                        $this->registerJs($birthDayPlugin);
                        echo $kyujinForm->field($apply, $columnName)
                            ->layout(function () use ($apply, $kyujinForm) {
                                $fieldOptions = ['template' => '{input}'];
                                //年
                                echo '<span class="field-apply-birthdateyear">';
                                echo $kyujinForm->field($apply, 'birthDateYear', $fieldOptions)
                                    ->dropDownList($apply->birthYearList, ['class' => 'form-control birth birthY', 'prompt' => '----']);
                                echo Html::tag('span', Yii::t('app', '年'), ['class' => 'form-unit']);
                                echo '</span>';
                                //月
                                echo '<span class="field-apply-birthdatemonth">';
                                echo $kyujinForm->field($apply, 'birthDateMonth', $fieldOptions)
                                    ->dropDownList($apply->birthMonthList, ['class' => 'form-control birth birthM', 'prompt' => '----']);
                                echo Html::tag('span', Yii::t('app', '月'), ['class' => 'form-unit']);
                                echo '</span>';
                                //日
                                echo '<span class="field-apply-birthdateday">';
                                echo $kyujinForm->field($apply, 'birthDateDay', $fieldOptions)
                                    ->dropDownList($apply->birthDayList, ['class' => 'form-control birth birthD', 'prompt' => '----']);
                                echo Html::tag('span', Yii::t('app', '日'), ['class' => 'form-unit']);
                                echo '</span>';
                                echo Html::activeHiddenInput($apply, 'birth_date');
                            });
                        $kyujinForm->breakLine();
                        break;
                    case 'tel_no':
                        echo $kyujinForm->row($apply,
                            $columnName)->textInput(['placeholder' => $item->column_explain]);
                        break;
                    case 'fullName':
                        //氏名
                        echo $kyujinForm->row($apply, $columnName)->pairTextInput('name_sei', 'name_mei',
                            ['placeholder' => $item->columnExplainSei],
                            ['placeholder' => $item->columnExplainMei]);
                        break;
                    case 'fullNameKana':
                        //フリガナ
                        echo $kyujinForm->row($apply, $columnName)->pairTextInput('kana_sei', 'kana_mei',
                            ['placeholder' => $item->columnExplainSei],
                            ['placeholder' => $item->columnExplainMei]);
                        break;
                    case 'pref_id':
                        // 郵便番号表示
                        if ($postalCodeFlg) {
                            // 重複表示回避のためフラグをfalseに
                            $postalCodeFlg = false;
                            echo $kyujinForm->row($apply, 'postalCode')->textInput();
                        }
                        echo $kyujinForm->row($apply, $columnName)->dropDownList(
                            Pref::getPrefList(),
                            ['prompt' => Yii::t('app', '--選択してください--')]
                        );
                        break;
                    case 'address':
                        // 郵便番号表示
                        if ($postalCodeFlg) {
                            // 重複表示回避のためフラグをfalseに
                            $postalCodeFlg = false;
                            echo $kyujinForm->row($apply, 'postalCode')->textInput();
                        }
                        echo $this->render('/common/_options-form', [
                            'kyujinForm' => $kyujinForm,
                            'columnSetModel' => $item,
                            'model' => $apply,
                        ]);
                        break;
                    case 'occupation_id':
                        //職業
                        echo $kyujinForm->row($apply, $columnName)->dropDownList(
                            ArrayHelper::map(Occupation::getOccupationList(), 'id', 'occupation_name'),
                            ['prompt' => Yii::t('app', '--選択してください--')]
                        );
                        break;
                    default:
                        echo $this->render('/common/_options-form', [
                            'kyujinForm' => $kyujinForm,
                            'columnSetModel' => $item,
                            'model' => $apply,
                        ]);
                        break;
                }
            }

            $kyujinForm->endTable();
            ?>

            <?= Html::hiddenInput('job_no', $jobMaster->job_no) ?>
            <p class="form-privacyLink text-center">
        <span class="privacyLink">
        <?= Yii::t('app', '{link}をご覧ください', [
            'link' => Html::a($policy->policy_name, 'javascript:void(0)', [
                'id' => 'policy',
                'data-url' => Url::to(['/policy', 'policy_no' => $policy->policy_no]),
            ]),
        ]) ?>
        </span>

            </p>

            <div class="mod-box-center w60 w90-sp">
                <?= Html::submitButton(Yii::t('app', '上記{policyName}に同意のうえ応募する', ['policyName' => $policy->policy_name]),
                    ['class' => 'mod-btn2']) ?>
            </div>
            <?php $kyujinForm->end(); ?>
        </div>
        <?php
        $script = <<<JS
$(function() {
    $("#policy").click(function(e) {
        window.open($(this).attr('data-url'), '_blank', 'width=800, height=800, scrollbars=yes');
    });
});
JS;
        $this->registerJs($script);
        ?>
    </div>
</div>
