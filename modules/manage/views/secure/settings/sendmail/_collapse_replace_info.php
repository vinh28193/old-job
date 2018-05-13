<?php
use yii\helpers\Html;

/* @var $model app\models\manage\SendMailSet */

$needleLabels = $model->needleLabels();
?>

<div class="well well-sm">
    <?= Html::tag('h5', Yii::t('app', '以下の置換文字は、メール送信時に対応する内容に置き換わります。'), ['class' => 'text-center']) ?>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="m-column text-center">
                    <div class="form-group"><?= Yii::t('app', '置換文字') ?></div>
                </th>
                <th class="m-column text-center">
                    <div class="form-group"><?= Yii::t('app', '置換内容') ?></div>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model->needles as $needle): ?>
                <tr>
                    <td>
                        <div class="form-group text-center"><?= $needle ?></div>
                    </td>
                    <td>
                        <div class="form-group text-center"><?= $needleLabels[$needle] ?></div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
