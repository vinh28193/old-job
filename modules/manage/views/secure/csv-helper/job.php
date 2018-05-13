<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/09/13
 * Time: 12:18
 */

use app\modules\manage\controllers\secure\CsvHelperController;
use yii\bootstrap\Html;

/** @var $searchkeyMaster \app\models\manage\SearchkeyMaster */
/** @var $helperType integer */
/** @var $pullDown array */

$js = <<<JS
$('#helperTypeDropDownList').on('change', function() {
    window.location.href = 'job?helperType=' + $(this).val();
});
JS;
$this->registerJs($js);

// todo by noboru :https://github.com/chornij/yii2-zeroclipboard コピーボタンはこれ使ってください。
// todo by noboru :zeroclipboardはgithubでも使われているライブラリですが、iosや一部androidでは使えません。

// todo by noboru :コピーのための文字列を吐くjsについて
// todo by noboru :チェックボックスがクリックされたら現在チェックされているものを見て文字列書き換え
// todo by noboru :pjax発動したら文字列リセット
$selectedJs = <<<JS
var dispChecked = (function() {
    var checkedList = [];
    return {
        check : function(val) {
            if(checkedList.indexOf(val) == -1){
                checkedList.push(val);
            }
        },
        uncheck : function(val) {
            if(checkedList.indexOf(val) >= 0){
                checkedList.splice(checkedList.indexOf(val), 1);
            }
        },
        checkAll : function(doms) {
            $(doms).each(function() {
                dispChecked.check($(this).val());
            });
        },
        uncheckAll : function(doms) {
            $(doms).each(function() {
                dispChecked.uncheck($(this).val());
            });
        },
        disp : function() {
            return checkedList.join('|');
        }
    };
})();

$(":checkbox").on("click", function() {
    if ($(this).hasClass("hasChildren")) {
        if($(this).prop('checked')){
            dispChecked.checkAll($(this).parent().next().find("input"));
        } else {
            dispChecked.uncheckAll($(this).parent().next().find("input"));
        }
        $('#selectSearchkey').text(dispChecked.disp());
        $(this).parent().next().find("input").prop("checked", $(this).prop("checked"));
    } else if ($(this).hasClass("hasParent")) {
        var parent = $(this).closest("div").prev().find("input");
        var children = $(this).closest("div");
        if (children.find(":checked").length == children.find("input").length) {
            parent.prop('checked', 'checked');
        } else {
            parent.prop('checked', false);
        }
    }
});

$(".hasChildren").each(function() {
    var children = $(this).closest("label").next();
    if (children.find(":checked").length == children.find("input").length) {
        $(this).prop('checked', 'checked');
    }
});

$('.panel-collapse').each(function() {
    if ($(this).find(":checked").length == 0) {
        $(this).collapse('hide');
    }
});

$('.dispChecked input').on('change', function () {
    if($(this).prop('checked')){
        dispChecked.check($(this).val());
    } else {
        dispChecked.uncheck($(this).val())
    }
    $('#selectSearchkey').text(dispChecked.disp());
});
JS;
$this->registerJs($selectedJs);

$style = <<<STYLE
#selectSearchkey {
    width: 50%;
    height: inherit;
    min-height: 34px;
    margin: auto;
    word-wrap: break-word;
}
STYLE;
$this->registerCss($style);
$clientChargePlanLabel = Yii::$app->functionItemSet->job->items['client_charge_plan_id']->label;
$this->title = Yii::t('app', '検索キーコード・' . $clientChargePlanLabel . 'の一覧');

?>
<h1 class="heading"><?= Html::icon('list-alt') . $this->title ?></h1>
<div class="container">
    <p class="alert alert-warning"><?= Yii::t('app',
            '下記のプルダウンを選択することで、検索キーコード・'.$clientChargePlanLabel.'の一覧が表示されます。'
            . '検索キーコードの一覧の場合、チェックボックスを選択することで、検索キーコードが"|"(パイプ)'
            . '区切りで表示されます。'
        ) ?></p>
    <div class="row">
        <div class="form-group mgb20">
            <?=
            \yii\bootstrap\Html::dropDownList(
                'helperType'
                , $helperType ?: 0
                , $pullDown
                , [
                    'id' => 'helperTypeDropDownList',
                    'class' => 'form-control select select-simple min-w',
                ]
            );
            ?>
        </div>
        <?php if ($helperType != CsvHelperController::PLAN): ?>
            <div class="form-group mgb20" style="text-align: center;">
                <div><label class="control-label"><?= Yii::t('app', '検索キーコード') ?></label></div>
                <div id="selectSearchkey" class="form-control select select-simple"></div>
            </div>
        <?php endif; ?>
        <?php
        switch ($helperType) {
            case CsvHelperController::PLAN:
                echo $this->render('job/_plan');
                break;
            case CsvHelperController::DIST:
                echo $this->render('job/_dist', ['searchkeyMaster' => $searchkeyMaster]);
                break;
            case CsvHelperController::JOB_TYPE:
                echo $this->render('job/_job-type', ['searchkeyMaster' => $searchkeyMaster]);
                break;
            case CsvHelperController::WAGE:
                echo $this->render('job/_wage', ['searchkeyMaster' => $searchkeyMaster]);
                break;
            case CsvHelperController::STATION:
                echo $this->render('job/_station');
                break;
            default:
                if($helperType >= CsvHelperController::CATE_1 && $helperType <= CsvHelperController::CATE_10)
                    echo $this->render('job/_cate', ['searchkeyMaster' => $searchkeyMaster]);
                if($helperType >= CsvHelperController::ITEM_11 && $helperType <= CsvHelperController::ITEM_20)
                    echo $this->render('job/_item', ['searchkeyMaster' => $searchkeyMaster]);
                break;
        }
        ?>
    </div>
</div>
