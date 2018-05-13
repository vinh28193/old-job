<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/07/25
 * Time: 18:31
 */
use yii\helpers\Html;

?>

<div class="col-sm-9 text-center">
    <?= Html::submitButton(Yii::t('app', '確定する（全項目適用）'), [
    'class' => 'btn btn-primary btn-sm',
    'data-toggle' => 'modal',
]) ?>
</div>
<div class="col-sm-3"></div>
