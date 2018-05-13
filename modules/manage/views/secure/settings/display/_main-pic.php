<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/07/09
 * Time: 12:55
 */
use app\assets\SwitchCheckboxAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model app\models\manage\MainDisplay */
/* @var $attribute string */

SwitchCheckboxAsset::register($this);

?>
<div class="row text-center">
    <div class="col-md-12">
        <?= Html::activeLabel($model, $attribute) ?>
    </div>
</div>
<div class="row text-center">
    <div class="col-md-12">
        <?= Html::activeCheckbox($model, $attribute, ['data-toggle' => 'switch', 'label' => null, 'data-on-color' => 'info']) ?>
    </div>
</div>