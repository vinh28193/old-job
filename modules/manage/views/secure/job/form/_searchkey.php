<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/11
 * Time: 15:12
 */

use app\models\manage\searchkey\JobStationInfo;
use app\models\manage\SearchkeyMaster;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/** @var \app\models\manage\JobMaster $model */
/** @var \proseeds\widgets\TableForm $tableForm */

$noSelectMessage = Yii::t('app', '指定なし');

// todo widget化
$selectedJs = <<<JS
var displayCheckedLabels = function(chkSelector, textSelector) {
  var selected = $(chkSelector+":checked").map(function() {
    return $(this).parent().text().trim();
  }).get().join();
  if (selected == "") {
     $(textSelector).text("{$noSelectMessage}");
  } else {
    $(textSelector).text(selected);
  }
}

$(":checkbox").on("change", function() {
    if ($(this).hasClass("hasChildren")) {
        // 全選択対象の子チェックボックス
        var children = $(this).parent().next().find("input");
        // 都道府県選択用の分岐
        if ($(this).hasClass("pref-selection-checkbox")) {
            var targetClass = $(this).attr("data-children-wrap-class");
            children = $("." + targetClass).children().find("input");
        }
        children.prop("checked", $(this).prop("checked"));
    } else if ($(this).hasClass("hasParent")) {
        var parent = $(this).closest("div").prev().find("input");
        var children = $(this).closest("div");
        // 都道府県選択用の分岐
        if ($(this).prop("name") == "JobDist[itemIds][]") {
            parent = $(this).parents(".panel").children().first().find("input")
        }
        if (children.find(":checked").length == children.find("input").length) {
            parent.prop('checked', 'checked');
        } else {
            parent.prop('checked', false);
        }
    }
});

$(".hasChildren").each(function() {
    var children = $(this).closest("label").next();
    // 都道府県選択用の分岐
    if ($(this).hasClass("pref-selection-checkbox")) {
        var targetClass = $(this).attr("data-children-wrap-class");
        children = $("." + targetClass).children();
    }
    if (children.find(":checked").length == children.find("input").length) {
        $(this).prop('checked', 'checked');
    }
});

displayCheckedLabels("[name='JobDist[itemIds][]']", "#distSelected");
$("[name='JobDist[itemIds][]']").on("change", function() {
    displayCheckedLabels("[name='JobDist[itemIds][]']", "#distSelected");
});
// 都道府県変更時の元画面ラベル設定処理
$('.pref-selection-checkbox').on("change", function() {
    displayCheckedLabels("[name='JobDist[itemIds][]']", "#distSelected");
});

$('.panel-collapse').each(function() {
  if ($(this).find(":checked").length == 0) {
    $(this).collapse('hide');
  }
});
JS;
$this->registerJs($selectedJs);

$tableForm->beginTable();
$searchKeys = SearchkeyMaster::findSearchKeys();
foreach ($searchKeys as $tableName => $searchKey) {
    /** @var SearchkeyMaster $searchKey */
    switch ($tableName) {
        case 'pref':
            echo $tableForm->field($model->jobDistModel, 'itemIds')->layout(function () use ($model, $searchKey) {
                echo Html::beginTag('div', ['id' => Html::getInputId($model->jobDistModel, 'itemIds')]);
                echo $this->render('_pref-modal', [
                    'model' => $model,
                    'searchKey' => $searchKey,
                ]);
                echo Html::label(Yii::t('app', '指定なし'), null, ['id' => 'distSelected']);
                echo Html::endTag('div');
            });
            echo $tableForm->breakLine();
            break;
        case 'station':
            echo $tableForm->field($model, 'jobStation')->layout(function () use ($tableForm, $model) {
                foreach ($model->jobStationModel as $i => $station) :
                    ?>
                    <div class="col-12 clearfix mgb10 pdb10" style="border-bottom: 1px solid #eee;">
                        <div class="col-md-3 pdt10">
                            <?= Html::tag('p', Yii::t('app', '最寄り駅' . ($i + 1))) ?>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('app', '駅名'); ?></label>
                                <div class="form-inline">
                                    <?= Select2::widget([
                                        'model' => $station,
                                        'attribute' => '[' . $i . ']station_id',
                                        'initValueText' => $station->stationName,
                                        'options' => ['placeholder' => Yii::t('app', '駅を選択してください')],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'minimumInputLength' => 1,
                                            'language' => [
                                                'inputTooShort' => new JsExpression('function () {return "1文字以上入力してください";}'),
                                            ],
                                            'ajax' => [
                                                'url' => Url::to('ajax-station'),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
                                            ],
                                        ]
                                    ]) ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('app', '駅からの交通手段'); ?></label>
                                <div class="form-inline">
                                    <?= Html::activeDropDownList($station, '[' . $i . ']transport_type', JobStationInfo::getTransportList(), ['class' => 'form-control select select-simple']) ?>
                                    で、<?= Html::activeTextInput($station, '[' . $i . ']transport_time', ['class' => 'form-control', 'size' => 7]); // todo 数字のclientValidation      ?>
                                    分
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php
            });
            echo $tableForm->breakLine();
            break;
        //給与
        case 'wage_category':
            echo $tableForm->field($model->jobWageModel, 'itemIds')->layout(function () use ($model, $searchKey) {
                foreach ($searchKey->searchKeyModels as $category) {
                    /** @var \app\models\manage\searchkey\WageCategory $category */
                    if ($category->wageItem) {
                        echo Html::label($category->wage_category_name, null, ['style' => 'margin-top: 1em;']);
                    }
                    $list = ArrayHelper::map($category->wageItem, 'id', 'disp_price');
                    $result = array_intersect($model->jobWageModel->itemIds, array_keys($list));
                    echo Html::activeDropDownList(
                        $model->jobWageModel,
                        'itemIds[]',
                        $list,
                        [
                            'class' => 'form-control select select-simple',
                            'prompt' => Yii::t('app', '給与を選択してください。'),
                            'value' => $result,
                        ]
                    );
                }
            });
            echo $tableForm->breakLine();
            break;
        case 'job_type_category':
            //職種
            echo $tableForm->field($model->jobTypeModel, 'itemIds')->layout(function () use ($model, $searchKey) {
                echo $this->render('_type-modal', [
                    'model' => $model,
                    'searchKey' => $searchKey,
                ]);
                echo Html::label(Yii::t('app', '指定なし'), null, ['id' => 'typeSelected']);
            });
            echo $tableForm->breakLine();
            break;
        default:
            if ($searchKeyModels = $searchKey->searchKeyModels) {
                if (preg_match('/searchkey_item\d+/', $tableName)) {
                    echo $tableForm->row($model->{$searchKey->jobRelationModelAttribute}, 'itemIds')->checkboxList(
                        ArrayHelper::map($searchKeyModels, 'id', 'searchkey_item_name'),
                        ['unselect' => null, 'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline']]]
                    );
                    echo $tableForm->breakLine();
                } else {
                    // todo widget化
                    echo $tableForm->field($model->{$searchKey->jobRelationModelAttribute}, 'itemIds')->layout(function () use ($model, $searchKey, $tableForm, $searchKeyModels) {
                        foreach ($searchKeyModels as $i => $category) {
                            /** @var \app\models\manage\searchkey\SearchkeyCategory $category */
                            if ($category->items) {
                                echo Html::label(Html::checkbox('selection_all', false, ['class' => 'hasChildren']) . $category->searchkey_category_name, null, ['style' => 'margin-top: 1em;']);
                            }
                            echo Html::activeCheckboxList(
                                $model->{$searchKey->jobRelationModelAttribute},
                                'itemIds',
                                ArrayHelper::map($category->items, 'id', 'searchkey_item_name'),
                                [
                                    'id' => $searchKey->jobRelationModelAttribute . '-' . $i,
                                    'unselect' => null,
                                    'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline'], 'class' => 'hasParent'],
                                ]
                            );
                        }
                    });
                    echo $tableForm->breakLine();
                }
                break;
            }

    };
}

$tableForm->endTable();