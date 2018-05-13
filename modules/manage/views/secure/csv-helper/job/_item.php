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

/** @var $searchKeyItems \app\models\manage\searchkey\SearchkeyItem[]|null */
$searchKeyItems = $searchkeyMaster->searchKeyModels;
?>
<div class="panel-group" id="pref-accordion" style="overflow-y: auto; height: 480px;">
    <?php
    echo Html::checkboxList(
        'Item',
        null,
        ArrayHelper::map($searchKeyItems, 'searchkey_item_no', 'searchkey_item_name'),
        [
            'unselect' => null,
            'itemOptions' => ['labelOptions' => ['class' => 'checkbox-inline dispChecked']]]
    );
    ?>
</div>
