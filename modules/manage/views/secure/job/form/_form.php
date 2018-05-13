<?php

use app\assets\JobBootBoxAsset;
use app\assets\JobDateAsset;
use app\common\PostablePjax;
use app\common\widget\FormattedDatePicker;
use app\models\manage\JobColumnSet;
use app\modules\manage\models\Manager;
use proseeds\widgets\PopoverWidget;
use uran1980\yii\assets\TextareaAutosizeAsset;
use yii\bootstrap\Modal;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use proseeds\widgets\TableForm;
use yii\web\JsExpression;
use yii\widgets\Pjax;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\models\manage\JobMaster */

/** @var Manager $identity */
$identity = Yii::$app->user->identity;

/* @var \app\common\ColumnSet $columnSet */
$columnSet = Yii::$app->functionItemSet->job;
/* @var JobColumnSet[] $items */
$items = $columnSet->items;

TextareaAutosizeAsset::register($this);
$defaultMsg = $model->isNewRecord ? Yii::t('app', '求人原稿情報を登録してもよろしいですか？') : Yii::t('app', '求人原稿情報を変更してもよろしいですか？');

JobBootBoxAsset::jobConfirmBeforeSubmit($this, $model);


$requirementsUrl = Url::toRoute('requirements');
if ($model->id) {
    $id = $model->id;
} elseif ($model->sourceId) {
    $id = $model->sourceId;
} else {
    $id = 0;
}

// ヒントデザイン調整（todo 共通cssへ入れる）
$hintCss = <<<CSS
.hint-block{
    color: #999;
    word-wrap: break-word;
    padding-top: 5px;
    padding-left: 1.3em;
    text-indent: -1.5em;
}
.hint-block span.glyphicon.glyphicon-info-sign{
    display: inline;
}
CSS;
$this->registerCss($hintCss);

if ($model->useReview()) {
    // 登録・審査依頼ボタンのデザイン調整
    $css = <<<CSS
#stackedSubmit {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    padding-left: 0px;
}
CSS;
    $this->registerCss($css);
}
// 掲載企業や代理店を変更した際、プランが自動で変更した後に掲載企業とプランをvalidationする
$depDropValidationJs = <<<JS
$('#jobmaster-client_charge_plan_id').on('depdrop.change', function(event, id, value, count) {
    $('#form').yiiActiveForm('validateAttribute', 'jobmaster-client_master_id');
    $('#form').yiiActiveForm('validateAttribute', 'jobmaster-client_charge_plan_id');
});
JS;
$this->registerJs($depDropValidationJs);

// todo php共々asset化してwidget化する必要がある
// todo validation失敗時にsubmitTypeが変になる
// クリックされたのが変更ボタンなのか、プレビューボタンなのかでpost先を振り分ける
// @see @app/controllers/KyujinController.php:221
// @see @app/modules/manage/views/secure/free-content/_form.php
// @see @app/modules/manage/views/secure/settings/header-footer-html/update.php
$newWindowSubmit = <<<JS
(function($) {
    $('#form').on('beforeSubmit', function(e){
        //どのsubmitボタンをクリックしたのか
        var submitType = $('#submitType').val();

        if(submitType == 'pcSubmit' || submitType == 'spSubmit'){
          var targetName = '';
          var actionUrl = '';
          if(submitType == 'spSubmit'){
              targetName = 'sp_preview';
              actionUrl = "/manage/preview?mode=Mobile";
              window.open("about:blank",targetName,"width=600, height=950, scrollbars=yes");
          } else if(submitType == 'pcSubmit'){
              targetName = 'pc_preview';
              actionUrl = "/manage/preview?mode=PC";
              window.open("about:blank",targetName,"width=1000, height=950, scrollbars=yes");
          }
          var target = document.input_form.target;
          var action = document.input_form.action;

          document.input_form.target = targetName;
          document.input_form.action = actionUrl;
          document.input_form.submit();

          document.input_form.target = target;
          document.input_form.action = action;
          document.getElementById("submitType").value = 'default';
          //以降の処理を中断
          e.stopImmediatePropagation();
        }
        return false;
    });
})(jQuery);

$("#stackedSubmit").on("click", function() {
  document.input_form.complete[0].click();
})
JS;
$this->registerJs($newWindowSubmit, View::POS_END);

// 入力モード毎に変数に代入する
if (!$identity->job_input_type) {
    // クラシックタイプの時
    $buttonSelector = '#classic-mode';
    $requirementsViewName = '_requirements';
} else {
    // プレビュータイプの時
    $buttonSelector = '#preview-mode';
    $requirementsViewName = '_classic-requirements';
}

// 入力モード切替ボタンjs
$changeModeUrl = Url::toRoute(['change-mode', 'selected' => !$identity->job_input_type]);
$message = Yii::t('app', '入力中の内容が反映されませんが入力モードの切り替えを行いますか？');
$changeModeJs = <<<JS
(function($) {
    var button = $("{$buttonSelector}");
    button.on("click", function(e) {
        bootbox.confirm({
            message: '{$message}',
            callback: function (result) {
                if (result) {
                    window.location = '{$changeModeUrl}'
                }
            }
        });
    });
})(jQuery);
JS;
$this->registerJs($changeModeJs);

// 更新日と終了日のvalidationを連動させるJs
// 運営元権限ではJobDateAsset内で対応しているため、代理店、掲載企業権限の時のみ中身が入る
$syncDatesJs = '';
?>
<div class="col-md-8">
    <div class="btn-group pull-right" style="z-index:1;">
        <?php
        if ($identity->job_input_type) {
            echo Html::a(Yii::t('app', 'プレビュー'), null, ['class' => 'btn btn-default', 'id' => 'preview-mode']);
            echo Html::a(Yii::t('app', 'クラシック'), null, ['class' => 'btn btn-primary', 'id' => 'classic-mode', 'style' => 'cursor:default;']);
        } else {
            echo Html::a(Yii::t('app', 'プレビュー'), null, ['class' => 'btn btn-primary', 'id' => 'preview-mode', 'style' => 'cursor:default;']);
            echo Html::a(Yii::t('app', 'クラシック'), null, ['class' => 'btn btn-default', 'id' => 'classic-mode']);
        }
        ?>
    </div>
</div>
<div class="col-md-10" role="complementary">
    <?php
    if (!$model->isNewRecord) {
        $copyUrl = Url::to(['copy', 'id' => $model->id]);
        echo Html::button(
            Html::icon('plus-sign') . Yii::t('app', 'この原稿をコピーして新規作成する'), [
                'class' => 'btn btn-primary pull-right',
                'onclick' => "javascript:location.href='{$copyUrl}'",
            ]
        );
    }
    ?>
    <div class="corp-master-form">
        <?php $tableForm = TableForm::begin([
            'action' => $model->isNewRecord ? Url::to('create') : Url::to(['update', 'id' => $model->id]),
            'id' => 'form',
            'options' => ['enctype' => 'multipart/form-data', 'name' => 'input_form'],
            'tableOptions' => ['class' => 'table table-bordered'],
            'validationUrl' => Url::to(['ajax-validation', 'id' => $model->isNewRecord ? 0 : $id]),
            'enableClientValidation' => false,
        ]);
        $selectClass = ['class' => 'select select-simple']; ?>
<!-- 基本情報 ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
        <div id="flow-01" class="table-heading">
            <span class="icon-nuki">1</span> <?= Yii::t('app', '基本情報を入力する'); ?>
        </div>
        <?php
        $tableForm->beginTable();
        echo $tableForm->field($model, 'job_no')->layout(function () use ($model, $items) {
            echo Html::encode($model->job_no ? $model->job_no : Yii::t('app', '※仕事IDは自動で採番されます'));
            echo Html::tag('div', $items['job_no']->explain, ['class' => 'hint-block']);
        });
        echo $tableForm->breakLine();
        // 代理店・掲載企業・プラン
        switch ($identity->myRole) {
            case Manager::OWNER_ADMIN:
                // 運営元はすべて入力可
                echo $this->render('_dep-drop', [
                    'model' => $model,
                    'tableForm' => $tableForm,
                    'inputs' => ['corp' => 'input', 'client' => 'input', 'plan' => 'input'],
                    'items' => $items
                ]);
                break;
            case Manager::CORP_ADMIN:
                // 代理店は新規作成時は掲載企業とプランが入力可で更新時は全て入力不可
                if ($model->isNewRecord) {
                    echo $this->render('_dep-drop', [
                        'model' => $model,
                        'tableForm' => $tableForm,
                        'inputs' => ['corp' => 'text', 'client' => 'input', 'plan' => 'input'],
                        'items' => $items
                    ]);
                } else {
                    echo $this->render('_dep-drop', [
                        'model' => $model,
                        'tableForm' => $tableForm,
                        'inputs' => ['corp' => 'text', 'client' => 'text', 'plan' => 'text'],
                        'items' => $items
                    ]);
                }
                break;
            case Manager::CLIENT_ADMIN:
                // 掲載企業は新規作成時はプランのみ入力可で変更時は全て入力不可
                if ($model->isNewRecord) {
                    echo $this->render('_dep-drop', [
                        'model' => $model,
                        'tableForm' => $tableForm,
                        'inputs' => ['corp' => 'text', 'client' => 'text', 'plan' => 'input'],
                        'items' => $items
                    ]);
                } else {
                    echo $this->render('_dep-drop', [
                        'model' => $model,
                        'tableForm' => $tableForm,
                        'inputs' => ['corp' => 'text', 'client' => 'text', 'plan' => 'text'],
                        'items' => $items
                    ]);
                }
                break;
            default :
                break;
        }
        // 掲載開始・終了日
        if ($identity->myRole == Manager::OWNER_ADMIN) {
            // 運営元はプラン及び新規or更新に関わらず終了日が入力可
            JobDateAsset::registerPlugin(true, $this);
            echo $tableForm->row($model, 'disp_start_date')->widget(FormattedDatePicker::className())
                ->hint($items['disp_start_date']->explain);
            echo $tableForm->row($model, 'disp_end_date', ['template' => "{th}\n{label}\n{/th}\n{td}\n{input}\n{hint}\n<div class='hint-block'>{$items['disp_end_date']->explain}</div>\n{error}\n{/td}"])
                ->hint(Yii::t('app', '{plan}の有効期間と入力された期間に差異があります(登録は可能です)', ['plan' => $items['client_charge_plan_id']->label]), ['id' => 'attention'])
                ->widget(FormattedDatePicker::className());
        } elseif ($model->isNewRecord) {
            // 代理店と掲載企業は新規登録時はプランに関わらず開始日を、期限自由プランなら終了日も編集可能
            JobDateAsset::registerPlugin(false, $this);
            echo $tableForm->row($model, 'disp_start_date')->widget(FormattedDatePicker::className())
                ->hint($items['disp_start_date']->explain);
            $datePickerOptions = ['options' => ['class' => 'form-control mgr10']];
            if (ArrayHelper::getValue($model, 'clientChargePlan.period', false) !== null) {
                $datePickerOptions['options']['style'] = 'display: none;';
            }
            echo $tableForm->row($model, 'disp_end_date', ['template' => '{th}{label}{/th}{td}{input}<p id="dispEndText"></p>{error}{/td}'])
                ->widget(FormattedDatePicker::className(), $datePickerOptions)
                ->hint($items['disp_end_date']->explain);
        } elseif ($model->clientChargePlan && $model->clientChargePlan->period === null) {
            // 代理店と掲載企業は更新時は期限自由プランの場合のみ開始日終了日を編集可能
            $syncDatesJs = <<<JS
$('#jobmaster-disp_start_date').change(function () {
    $('#form').yiiActiveForm('validateAttribute', 'jobmaster-disp_end_date');
});
JS;
            echo $tableForm->row($model, 'disp_start_date')->widget(FormattedDatePicker::className())
                ->hint($items['disp_start_date']->explain);
            echo $tableForm->row($model, 'disp_end_date')->widget(FormattedDatePicker::className())
                ->hint($items['disp_end_date']->explain);
        } else {
            // 代理店と掲載企業の更新時は期限自由プラン以外編集不可
            echo $tableForm->row($model, 'disp_start_date')->text('date');
            if (!$model->disp_end_date) {
                echo $tableForm->row($model, 'disp_end_date')->text('date');
            } else {
                echo $tableForm->row($model, 'disp_end_date')->text('date');
            }
        }
        // 公開or非公開
        echo $tableForm->row($model, 'valid_chk')->radioList([JobColumnSet::VALID => Yii::t('app', '公開'),JobColumnSet::INVALID => Yii::t('app', '非公開')]);
        $tableForm->endTable();
        ?>
<!-- 募集要項 ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
        <div id="flow-02" class="table-heading">
            <span class="icon-nuki">2</span> <?= Yii::t('app', '募集要項を入力する'); ?>
            <?= PopoverWidget::widget(['dataContent' => Yii::t('app', '編集したい箇所をクリック（タップ）することで編集できます。')]) ?>
        </div>
        <?php
        // プレビュー版のみ項目名表示ボタンを表示
        if (!$identity->job_input_type) {
            echo Html::a(Yii::t('app', '項目名を表示・非表示'), null, [
                'id' => 'showLabels',
                'tabindex' => 0,
                'class' => 'btn btn-sm btn-simple',
                'role' => 'button'
            ]);
        }

        PostablePjax::begin([
            'id' => 'requirementsContent',
            'enablePushState' => false,
            'url' => Url::toRoute('requirements'),
            'trigger' => [
                ['selector' => '"#jobmaster-client_charge_plan_id"', 'event' => 'change'],
                ['selector' => '"#jobmaster-client_charge_plan_id"', 'event' => 'depdrop.change'],
            ],
            'post' => [
                'id' => $id,
                'clientChargePlanId' => new JsExpression('$("#jobmaster-client_charge_plan_id").val()'),
                'inputType' => new JsExpression('$("#jobmaster-client_charge_plan_id").val()'),
            ],
        ]);

        echo $this->render($requirementsViewName, [
            'id' => $id,
            'model' => $model,
            'dispTypeId' => $model->clientChargePlan ? $model->clientChargePlan->disp_type_id : null,
            'tableForm' => $tableForm,
        ]);

        PostablePjax::end()
        ?>

<!-- 検索条件 ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
        <div id="flow-03" class="table-heading">
            <span class="icon-nuki">3</span> <?= Yii::t('app', '検索条件を入力する'); ?>
        </div>
        <?= $this->render('_searchkey', [
            'model' => $model,
            'tableForm' => $tableForm,
        ]) ?>

<!-- 応募情報 ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
        <div id="flow-04" class="table-heading">
            <span class="icon-nuki">4</span> <?= Yii::t('app', '応募情報を入力する'); ?>
            <?= PopoverWidget::widget([
                'dataHtml' => true,
                'dataContent' => Yii::t('app', '電話番号を入力すると、サイト上に電話応募ボタンが表示されます。<br>応募先メールアドレスを入力すると、サイト上に応募するボタンが表示されます。未入力の場合はサイトからのエントリーができない状態になりますのでお気を付けください。'),
            ]) ?>
        </div>
        <?php
        $tableForm->beginTable();
        //応募先電話番号1
        if (ArrayHelper::keyExists('application_tel_1', $items)) {
            echo $tableForm->row($model, 'application_tel_1', [
                'enableAjaxValidation' => false,
            ])->textInput()
                ->hint($items['application_tel_1']->explain);
        }
        //応募先電話番号2
        if (ArrayHelper::keyExists('application_tel_2', $items)) {
            echo $tableForm->row($model, 'application_tel_2', [
                'enableAjaxValidation' => false,
            ])->textInput()
                ->hint($items['application_tel_2']->explain);
        }
        //応募先メールアドレス
        if (ArrayHelper::keyExists('application_mail', $items)) {
            echo $tableForm->row($model, 'application_mail', [
                'enableAjaxValidation' => false,
            ])->textInput()
                ->hint($items['application_mail']->explain);
        }
        //営業担当者
        if (ArrayHelper::keyExists('agent_name', $items)) {
            echo $tableForm->row($model, 'agent_name')->textInput()
                ->hint($items['agent_name']->explain);
        }
        //自動配信メール文面
        if (ArrayHelper::keyExists('mail_body', $items)) {
            echo $tableForm->row($model, 'mail_body')->textarea(['rows' => 5])
                ->hint($items['mail_body']->explain);
        }

        $tableForm->endTable();
        ?>
<?php // 審査機能ONの場合のみ表示 ?>
<?php if (Yii::$app->tenant->tenant->review_use) : ?>
<!-- 審査情報 ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
        <div id="flow-05" class="table-heading">
            <span class="icon-nuki">5</span> <?= Yii::t('app', '審査状況を確認する'); ?>
            <?= PopoverWidget::widget([
                'dataHtml' => true,
                'dataContent' => Yii::t('app', '現在の審査状況を確認できます。'),
            ]) ?>
        </div>
        <?php
        echo $this->render('/secure/job-review/common/_review-history', ['id' => $model->id]);
        ?>
<?php endif; ?>
<!-- ボタン ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
        <p class="text-right">
            <?php
            echo Html::submitButton(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'PC版プレビュー'),
                [
                    'class' => 'btn btn-warning mgt10 mgr20',
                    'onclick' => 'document.getElementById("submitType").value = "pcSubmit";',
                ]);
            echo Html::submitButton(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-eye-open']) . Yii::t('app', 'スマホ版プレビュー'),
                [
                    'class' => 'btn btn-warning mgt10',
                    'onclick' => 'document.getElementById("submitType").value = "spSubmit";',
                ]
            );
            echo Html::hiddenInput('submitType', 'default', ['id' => 'submitType']);
            ?>
        </p>
        <?= $this->render('/secure/common/_form-buttons.php', [
            'model' => $model,
        ]); ?>
        <?php
        echo Html::hiddenInput('complete');
        $tableForm->attributes = [];
        foreach ($items as $item) {
            /** @var JobColumnSet $item */
            switch ($item->column_name) {
                case'job_no':
                case'corpLabel':
                    break;
                case'client_master_id':
                    if ($identity->role == Manager::OWNER_ADMIN || $identity->role == Manager::CORP_ADMIN) {
                        $tableForm->form($model, $item->column_name, ['enableAjaxValidation' => true, 'enableClientValidation' => false])->begin();
                    }
                    break;
                default:
                    $tableForm->form($model, $item->column_name, ['enableAjaxValidation' => true, 'enableClientValidation' => false])->begin();
                    break;
            }
        }
        $tableForm->form($model, 'valid_chk', ['enableAjaxValidation' => true, 'enableClientValidation' => false])->begin();
        if ($identity->role == Manager::OWNER_ADMIN) {
            $tableForm->form($model, 'corpMasterId', ['enableAjaxValidation' => true, 'enableClientValidation' => false])->begin();
        }
        $tableForm->form($model->jobDistModel, 'itemIds', ['enableAjaxValidation' => true, 'enableClientValidation' => false])->begin();

        TableForm::end();
        // 読み込み順の都合でここでregisterします
        $this->registerJs($syncDatesJs); ?>
    </div>
</div>
<!-- stacked-nav ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
<?php
// 審査機能ON かつ 求運営元管理者以外の場合、ボタンメッセージを変える
if (Yii::$app->tenant->tenant->review_use && !Yii::$app->user->identity->isOwner()) {
    $submitButtonMsg = $model->isNewRecord ? Yii::t('app', '登録・審査依頼') : Yii::t('app', '変更・審査依頼');
} else {
    $submitButtonMsg = $model->isNewRecord ? Yii::t('app', '登録する') : Yii::t('app', '変更する');
}

?>
<div id="sidebar-right-wrap" class="col-md-2">
    <nav id="sidebar-right" data-spy="affix">
        <ul class="nav nav-pills nav-stacked">
            <li><a href="#flow-01">1. <?= Yii::t('app', '基本情報'); ?></a></li>
            <li><a href="#flow-02">2. <?= Yii::t('app', '募集要項'); ?></a></li>
            <li><a href="#flow-03">3. <?= Yii::t('app', '検索条件'); ?></a></li>
            <li><a href="#flow-04">4. <?= Yii::t('app', '応募情報'); ?></a></li>
        <?php // 審査機能ONの場合のみ表示 ?>
        <?php if (Yii::$app->tenant->tenant->review_use) : ?>
            <li><a href="#flow-05">5. <?= Yii::t('app', '審査状況'); ?></a></li>
        <?php endif; ?>
            <li><?php echo Html::button(
                Html::icon('pencil') . $submitButtonMsg, [
                        'id' => 'stackedSubmit',
                        'class' => 'btn btn-primary w100 mgt20',
                        'name' => 'complete',
                    ]
                ); ?>
            </li>
            <?php // 審査機能ONかつ既存レコードかつログイン管理者の審査対象の場合のみ表示 ?>
            <?php if ($model->useReview() && $model->isReview()) : ?>
                <?php echo Html::a(
                    Html::icon('check') . Yii::t('app', '審査する'),
                    Url::to(['/manage/secure/job-review/pjax-modal', 'id' => $model->id]),
                    [
                        'class' => 'pjaxModal btn btn-danger w100 mgt20',
                    ]
                ); ?>
            <?php endif; ?>
        </ul>
    </nav>
</div>
<!-- 画像モーダル ――――――――――――――――――――――――――――――――――――――――――――――――――― -->
<?php
$picUrl = Url::to('pic');
$picPjaxJs = <<<JS
var PictureColumnNameClicked;
var renderPictureModalColtents = (function() {
    var clientMasterId = null;
    return function(columnName){
        $("#picModal").modal();
        PictureColumnNameClicked = columnName;
        var newClientMasterId = $("#jobmaster-client_master_id").val();
        if (clientMasterId == null || clientMasterId != newClientMasterId) {
            clientMasterId = newClientMasterId;
            jQuery.pjax({
                "push": false,
                "replace": false,
                "cache": false,
                "timeout": 1000,
                "scrollTo": false,
                "url": "\/manage\/secure\/job\/pic",
                "type": "POST",
                "container": "#picContent",
                "area": "#picContent",
                "data": {"clientMasterId": clientMasterId},
             })
        }
    };
})();
JS;
$this->registerJs($picPjaxJs, $this::POS_END);

Modal::begin([
    'size' => Modal::SIZE_LARGE,
    'options' => ['id' => 'picModal'],
    'header' => '<h3 class="modal-title pull-left" id="myModalLabel"><span class="glyphicon glyphicon-list-alt"></span>' . Yii::t('app', '写真-登録・修正') . '</h3>',
]); ?>
<div id="selectPhoto">
    <div>
        <pre class="description"><?= Yii::t('app', '推奨画像サイズ：800 x 600pixel以上 または 200万画素以上を推奨
対応ファイル形式：jpeg, gif, png, bmp, eps, tiff, psd
※掲載する写真には他者（個人・法人）が著作権を有する画像を使用しない事を、厳守願います。') ?></pre>

        <div id="check-form">
            <?php
            Pjax::begin([
                'enablePushState' => false,
                'formSelector' => '#upload-form',
                'options' => ['id' => 'picContent']
            ]);
            Pjax::end() ?>
        </div>
    </div>
</div>
<?php Modal::end();

// 審査モーダル
// 審査機能ONかつ既存レコードかつログイン管理者の審査対象の場合のみ表示
if ($model->useReview() && $model->isReview()) {
    Pjax::begin([
        'id' => 'pjaxModal',
        'enablePushState' => false,
        'linkSelector' => '.pjaxModal',
    ]);
    Pjax::end();
}