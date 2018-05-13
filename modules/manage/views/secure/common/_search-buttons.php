<?php

use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $model \yii\db\ActiveRecord */
?>

<div class="row mgt20">
    <p class="text-center">
        <?php
        echo Html::a(Yii::t('app', 'クリア'), Url::to(['list', $model->formName() => true]), ['class' => 'btn btn-simple']);
        echo Html::submitButton('<span class="glyphicon glyphicon-search"></span>この条件で表示する', ['class' => 'btn btn-primary btn-lg mgl20']);
        ?>
    </p>
</div>
        