<?php
use yii\bootstrap\Modal;
use yii\helpers\Html;
use proseeds\widgets\TableForm;
use yii\helpers\Url;
use yii\widgets\Pjax;
use proseeds\assets\PjaxModalAsset;

/* @var $this \yii\web\View */
/* @var $isNew */
/* @var $flg */
/* @var $attribute */

Pjax::begin([
    'id' => 'pjaxModal',
    'enablePushState' => false,
    'linkSelector' => '.pjaxModal',
]);
if (Yii::$app->requestedRoute != 'manage/secure/prefdist/list') {
    $this->registerJs('$("#' . ($isNew ? $flg . 'modal-new' : $flg . 'modal-' . $model->id) . '").modal("show");');
}
PjaxModalAsset::register($this);

//削除メッセージの作成
$message = '削除したものは元に戻せません。削除しますか？';
switch ($attribute['page']) {
    case 'searchkey2':
        if ($flg == 'first') {
            $message = Yii::t('app', '削除したものは元に戻せません。削除しますか？選択したカテゴリに設定されている項目も削除されます。ご注意ください。');
        }
        break;
    case 'jobtype':
        if ($flg == 'first') {
            $message = Yii::t('app', '削除したものは元に戻せません。削除しますか？選択したカテゴリに設定されている項目も削除されます。ご注意ください。');
        } else {
            if ($flg == 'second') {
                $message = Yii::t('app', '削除したものは元に戻せません。削除しますか？選択したグループに設定されている項目も削除されます。ご注意ください。');
            }
        }
        break;
    case 'wage':
        if ($flg == 'first') {
            $message = Yii::t('app', '削除したものは元に戻せません。削除しますか？選択したカテゴリに設定されている項目も削除されます。ご注意ください。');
        }
        break;
    default:
        break;
}

//delete時に別アクションにpostする必要があるため、下記のような書き方にしている。
$url = Url::toRoute(array_merge(['delete'], Yii::$app->request->get() + ['flg' => $flg, 'id' => $model->id, 'page' => $attribute['page']]));
$js = <<<JS
(function($) {
  var mess = "{$message}";
  $('#delete').click(function(){
  	var form = $(this).parents('form');
    bootbox.confirm(mess, function(result){
      if (result) {
        $(form).attr('action', "{$url}");
        $(form).submit();
      }
    });
  });
})(jQuery);
JS;
$this->registerJs($js);
$tableForm = TableForm::begin([
    'id' => 'searchkeyForm',
    'action' => [
        Url::toRoute(array_merge([$isNew ? 'create' : 'update'], Yii::$app->request->get() + ['flg' => $flg, 'id' => $model->id]))
    ],
    'options' => ['enctype' => 'multipart/form-data'],
    'tableOptions' => ['class' => 'table table-bordered'],
    'validationUrl' => Url::to(['ajax-validation', 'id' => $isNew ? '0' : $model->id]),
]);

$fieldOptions = ['tableHeaderOptions' => ['class' => 'm-column']];

Modal::begin([
    'id' => $isNew ? $flg . 'modal-new' : $flg . 'modal-' . $model->id,
    'header' => Yii::t('app', $isNew ? '登録' : '変更'),
    'footer' =>
        ($isNew || ($attribute['page'] == 'area') ? '' : Html::a(Yii::t('app', '削除'), 'javascript:void(0)', ['class' => 'btn btn-simple pull-left', 'id' => 'delete']) . ' ')
        . Html::submitButton(Yii::t('app', $isNew ? '登録' : '変更'), ['class' => 'btn btn-primary submitUpdate']),
]);
?>

<?php
$tableForm->beginTable();

if ($attribute['page'] == 'area') {
    switch ($flg) {
        case 'first':
            echo $tableForm->row($model, 'area_name', $fieldOptions)->textInput();
            echo $tableForm->row($model, 'area_dir', ['options' => ['class' => 'form-group form-inline']])
                ->layout(function () use ($model, $tableForm) {
                    echo Html::encode(Yii::t('app', Yii::$app->request->getHostInfo())) . '/ ';
                    echo Html::activeInput('type', $model, 'area_dir', ['class' => 'form-control']);
                    echo '<span class="glyphicon form-control-feedback" aria-hidden="true"></span>';
                });
            echo $tableForm->row($model, 'valid_chk', ['enableAjaxValidation' => true])->radioList([
                1 => '公開',
                0 => '非公開',
            ]);
            break;
        case 'second':
            break;
        default:
    }
} else {
    if ($attribute['page'] == 'prefdist') {
        switch ($flg) {
            case 'first':
                echo $tableForm->row($model, 'pref_dist_name', $fieldOptions + ['enableAjaxValidation' => true])->textInput(['maxlength' => true]);
                echo $tableForm->row($model, 'sort', $fieldOptions)->textInput(['maxlength' => true]);
                echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([1 => '公開', 0 => '非公開']);
                echo Html::hiddenInput('PrefDistMaster[pref_id]', Yii::$app->request->get('PrefDistMaster')['pref_id']);
                break;
            default:
        }
    } else {
        if ($attribute['page'] == 'jobtype') {
            switch ($flg) {
                case 'first':
                    echo $tableForm->row($model, 'name', $fieldOptions)->textInput(['maxlength' => true]);
                    echo $tableForm->row($model, 'sort', $fieldOptions)->textInput();
                    echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([1 => '公開', 0 => '非公開']);

                    break;
                case 'second':
                    echo $tableForm->row($model, 'job_type_category_id', $fieldOptions)
                        ->dropDownList($attribute['dropDownList'][0], ['class' => 'select select-simple form-control']);
                    echo $tableForm->row($model, 'job_type_big_name', $fieldOptions)->textInput(['maxlength' => true]);
                    echo $tableForm->row($model, 'sort', $fieldOptions)->textInput();
                    echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([1 => '公開', 0 => '非公開']);

                    break;
                case 'thread':
                    echo $tableForm->row($model, 'job_type_big_id', $fieldOptions)
                        ->dropDownList($attribute['dropDownList'][1], ['class' => 'select select-simple form-control']);
                    echo $tableForm->row($model, 'job_type_small_name', $fieldOptions)->textInput(['maxlength' => true]);
                    echo $tableForm->row($model, 'sort', $fieldOptions)->textInput();
                    echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([1 => '公開', 0 => '非公開']);

                    break;
                default:
            }
        } else {
            if ($attribute['page'] == 'wage') {
                switch ($flg) {
                    case 'first':
                        echo $tableForm->row($model, 'wage_category_name', $fieldOptions)->textInput(['maxlength' => true]);
                        echo $tableForm->row($model, 'sort', $fieldOptions)->textInput();
                        echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([1 => '公開', 0 => '非公開']);

                        break;
                    case 'second':
                        echo $tableForm->row($model, $attribute['groupId'], $fieldOptions)
                            ->dropDownList($attribute['dropDownList'][0], ['class' => 'select select-simple form-control']);
                        echo $tableForm->row($model, 'disp_price', $fieldOptions)->textInput(['maxlength' => true]);
                        echo $tableForm->row($model, 'wage_item_name', $fieldOptions)->textInput(['maxlength' => true]);
                        echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([1 => '公開', 0 => '非公開']);

                        break;
                    default:
                }
            } else {
                if ($attribute['page'] == 'searchkey1') {
                    switch ($flg) {
                        case 'first':
                            echo $tableForm->row($model, 'searchkey_item_name', $fieldOptions)
                                ->textInput(['maxlength' => true]);
                            echo $tableForm->row($model, 'sort', $fieldOptions)->textInput();
                            echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([
                                1 => '公開',
                                0 => '非公開',
                            ]);

                            break;
                        default:
                    }
                } else {
                    switch ($flg) {
                        case 'first':
                            echo $tableForm->row($model, 'searchkey_category_name', $fieldOptions)
                                ->textInput(['maxlength' => true]);
                            echo $tableForm->row($model, 'sort', $fieldOptions)->textInput();
                            echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([
                                1 => '公開',
                                0 => '非公開',
                            ]);

                            break;
                        case 'second':
                            echo $tableForm->row($model, 'searchkey_category_id', $fieldOptions)
                                ->dropDownList($attribute['dropDownList'][0], ['class' => 'select select-simple form-control']);
                            echo $tableForm->row($model, 'searchkey_item_name', $fieldOptions)
                                ->textInput(['maxlength' => true]);
                            echo $tableForm->row($model, 'sort', $fieldOptions)->textInput();
                            echo $tableForm->row($model, 'valid_chk', $fieldOptions)->radioList([
                                1 => '公開',
                                0 => '非公開',
                            ]);

                            break;
                        default:
                    }
                }
            }
        }
    }
}

$tableForm->endTable();
?>

<?php
Modal::end();
TableForm::end();
Pjax::end();
