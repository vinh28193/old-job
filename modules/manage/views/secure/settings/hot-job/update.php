<?php

use app\models\manage\ManageMenuMain;
use yii\bootstrap\Html;
use app\models\manage\HotJob;
use yii\helpers\Url;
use proseeds\assets\BootBoxAsset;
use proseeds\widgets\TableForm;
use kartik\sortinput\SortableInput;

$this->title = ManageMenuMain::findFromRoute(Url::toRoute('update'))->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;

BootBoxAsset::confirmBeforeSubmit($this, Yii::t("app", "{$this->title}を変更してもよろしいですか？"));

$tableForm = TableForm::begin([
    'id' => 'form',
    'action' => 'update?id=' . $model->id,
    'tableOptions' => ['class' => 'table table-bordered'],
    'enableClientValidation' => true,
    'validationUrl' => Url::to(['ajax-validation', 'id' => $model->id]),
]);
?>

<h1 class="heading"><?= Html::icon('wrench') . Html::encode($this->title) ?></h1>

<?php
if (Yii::$app->session->getFlash('updateError')):
    echo Html::tag('p', Yii::$app->session->getFlash('updateError'), ['class' => 'alert alert-danger']);
endif;
?>

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <p class="alert alert-warning"><?= Yii::t('app', 'トップ画面に表示される注目情報を設定します。'); ?> <br>
                <?= Yii::t('app', '優先項目はドラッグ＆ドロップで順番を並び替えられます。（左から「優先度高」となります）') ?><br>
                <?= Yii::t('app', '「変更」ボタンをクリックすることで変更できます。') ?><br>
            </p>

            <?= Yii::$app->session->getFlash('operationComment') ?>

            <div class="headerFooter-form">
                <?php
                $tableForm->beginTable();

                echo $tableForm->row($model, 'valid_chk')->radioList(HotJob::getValidChkLabels());

                echo $tableForm->row($model, 'title', ['inputOptions' => ['class' => 'form-control']])->textInput();
                echo $tableForm->row($model, 'disp_amount',
                    ['inputOptions' => ['class' => 'form-control']])->textInput();

                echo $tableForm->row($model, 'item')->isRequired(true)->layout(function () use ($model, $tableForm) {
                    $priorityItems = [];
                    foreach ($model->hotJobPriority as $priority) {
                        $priorityItems[$priority->disp_priority] = [
                            'id' => $priority->id,
                            'content' => $model->getAttributeLabel($priority->item)
                        ];
                    }
                    ksort($priorityItems);

                    $hotJobPriority = SortableInput::widget([
                        'name' => 'hotJobPriority',
                        'items' => $priorityItems,
                        'sortableOptions' => [
                            'itemOptions' => ['class' => 'btn btn-simple'],
                            'connected' => true,
                        ],
                        'options' => ['class' => 'form-control', 'readonly' => true]
                    ]);
                    $content = "<div>{$hotJobPriority}</div>";
                    echo $content;
                });

                echo $tableForm->row($model, 'disp_type_label')->isRequired(true)->layout(function () use (
                    $model,
                    $tableForm,
                    $dispTypeName
                ) {
                    echo $tableForm->form($model, 'disp_type_ids',
                        ['inputOptions' => ['class' => 'form-control']])->checkboxList($dispTypeName);
                });

                for ($i = 1; $i <= 4; $i++) {
                    echo $tableForm->row($model, 'text' . $i)->layout(function () use (
                        $model,
                        $tableForm,
                        $i,
                        $jobColumnSet
                    ) {
                        echo $tableForm->labelForm($model, 'text' . $i,
                            ['inputOptions' => ['class' => 'form-control']])->dropDownList([
                                '' => Yii::t('app', '表示なし')
                            ] + $jobColumnSet)->label(Yii::t('app', '表示項目'));
                        echo $tableForm->labelForm($model, 'text' . $i . '_length',
                            ['inputOptions' => ['class' => 'form-control']])->textInput()->label(Yii::t('app', '文字数上限'));
                    });
                }

                $tableForm->endTable();

                echo '<p class="text-center">';
                echo Html::submitButton(Yii::t('app', '変更する'), ['class' => 'btn btn-primary', 'name' => 'complete']);
                echo Html::hiddenInput('submitType', 'default', ['id' => 'submitType']);
                echo '</p>';
                TableForm::end();
                ?>
            </div>

        </div>
    </div>
</div>