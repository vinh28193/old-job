<?php
/**
 * Created by PhpStorm.
 * User: mita33
 * Date: 2016/10/04
 * Time: 18:28
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\manage\searchkey\WageCategory;

/** @var $searchkeyMaster \app\models\manage\SearchkeyMaster */

/** @var WageCategory[]|null $wageCategories */
$wageCategories = $searchkeyMaster->searchKeyModels;

$js = <<<JS

var dispWageChecked = (function() {
    var checkedList = [];
    return {
        check : function(val, cate) {
            if(checkedList[cate] == null){
                checkedList[cate] = [];
            }
            if((checkedList[cate]).indexOf(val) == -1){
                (checkedList[cate]).push(val);
            };
        },
        uncheck : function(val, cate) {
            if(checkedList[cate] == null){
                checkedList[cate] = [];
            }
            if((checkedList[cate]).indexOf(val) >= 0){
                (checkedList[cate]).splice((checkedList[cate]).indexOf(val), 1);
            }
        },
        dispWage : function() {
            var text = '';
            for(cate in checkedList){
                if(text !== ''){
                    text += '<br>';
                }
                if((checkedList[cate]).length > 0){
                    text += cate + ':' + Math.max.apply(null, (checkedList[cate]));
                }
            }
            return text;
        },
    };
})();
$("[name='WageItem[]']").on("change", function() {
    var cate = $($(this).parents('div')[0]).prev('.wageCategoryName').text();
    if ($(this).prop("checked") == true) {
        dispWageChecked.check($(this).val(), cate);
        var prevCheckbox = $(this).parent().prev().children(":checkbox");
        while (prevCheckbox.length == 1) {
            prevCheckbox.prop("checked", true);
            dispWageChecked.check($(prevCheckbox).val(), cate);
            prevCheckbox = $(prevCheckbox).parent().prev().children(":checkbox");
        }
        $('#selectSearchkey').html(dispWageChecked.dispWage());
    } else if ($(this).prop("checked") == false) {
        dispWageChecked.uncheck($(this).val(), cate);
        var nextCheckbox = $(this).parent().next().children(":checkbox");
        while (nextCheckbox.length == 1) {
            nextCheckbox.prop("checked", false);
            dispWageChecked.uncheck($(nextCheckbox).val(), cate);
            nextCheckbox = $(nextCheckbox).parent().next().children(":checkbox");
        }
        $('#selectSearchkey').html(dispWageChecked.dispWage());
    }
});
JS;
$this->registerJs($js);
?>
<div class="panel-group" id="pref-accordion" style="overflow-y: auto; height: 480px;">
    <?php
    foreach ($wageCategories as $category) {
        if ($category->wageItem) {
            echo Html::label($category->wage_category_name, null, ['style' => 'margin-top: 1em;', 'class' => 'wageCategoryName']);
        }
        echo Html::checkboxList(
            'WageItem',
            null,
            ArrayHelper::map($category->wageItem, 'wage_item_name', 'disp_price'),
            ['unselect' => null, 'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline wageDispChecked']]]
        );
    }
    ?>
</div>
