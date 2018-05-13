<?php
/**
 * Created by PhpStorm.
 * User: mita33
 * Date: 2016/10/05
 * Time: 15:00
 */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/** @var $searchkeyMaster \app\models\manage\SearchkeyMaster */

/** @var $searchKeyCategories \app\models\manage\searchkey\SearchkeyCategory[]|null */
$searchKeyCategories = $searchkeyMaster->searchKeyModels;

?>
<div class="panel-group" id="pref-accordion" style="overflow-y: auto; height: 480px;">
    <?php
    foreach ($searchKeyCategories as $i => $category) {
        if ($category->items) {
            echo Html::label(
                Html::checkbox('selection_all', false, ['class' => 'hasChildren']) . $category->searchkey_category_name
                , null
                , ['style' => 'margin-top: 1em;']
            );
        }
        echo Html::checkboxList(
            'Item',
            null,
            ArrayHelper::map($category->items, 'searchkey_item_no', 'searchkey_item_name'),
            [
                'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline dispChecked'], 'class' => 'hasParent'],
            ]
        );
    }
    ?>
</div>
