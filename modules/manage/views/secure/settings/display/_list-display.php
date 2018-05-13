<?php
/* @var $displayItems app\models\manage\JobColumnSet[]|\app\models\manage\ClientColumnSet[] */
/* @var $notDisplayItems app\models\manage\JobColumnSet[]|\app\models\manage\ClientColumnSet[] */
?>
<div class="col-sm-9 sort-wrapper">
    <?= $this->render('_sortable', ['items' => $displayItems, 'name' => 'ListItems', 'connected' => 'list-items']); ?>
</div>

<div class="col-sm-3">
    <h3 style="margin-top: 0"><?= Yii::t('app', '表示することのできる求人原稿項目') ?></h3>
    <p class="mgb20"><?= Yii::t('app', '左レイアウト内の希望の表示箇所に、以下の求人原稿項目をドラッグ＆ドロップしてください。') ?></p>
    <?= $this->render('_sortable', ['items' => $notDisplayItems, 'name' => 'NotListItems', 'connected' => 'list-items']); ?>
</div>
<?= $this->render('_submit-button'); ?>
