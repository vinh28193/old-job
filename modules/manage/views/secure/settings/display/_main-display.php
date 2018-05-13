<?php
/* @var $this \yii\web\View */
/* @var $dispTypeId integer */
/* @var $mainDisplayModel app\models\manage\MainDisplay */
/* @var $notMainItems app\models\manage\JobColumnSet[] */

$css = <<<CSS
.tall ul {
  min-height:140px
}
CSS;
$this->registerCss($css);

?>
<div class="col-sm-9 sort-wrapper">
    <div class="row">
        <div class="col-sm-12 attributeSortable">
            <?= $this->render('_main-sortable', ['model' => $mainDisplayModel, 'attribute' => 'main', 'form' => $form]) ?>
            <?= $this->render('_main-sortable', ['model' => $mainDisplayModel, 'attribute' => 'title', 'form' => $form]) ?>
            <?= $this->render('_main-sortable', ['model' => $mainDisplayModel, 'attribute' => 'title_small', 'form' => $form]) ?>

            <div class="row tall">
                <div class="col-md-4">
                    <?= $this->render('_main-pic', ['model' => $mainDisplayModel, 'attribute' => 'pic1']) ?>
                </div>
                <div class="col-md-8">
                    <?= $this->render('_main-sortable', ['model' => $mainDisplayModel, 'attribute' => 'comment', 'form' => $form]) ?>
                </div>
            </div>

            <?= $this->render('_main-sortable', ['model' => $mainDisplayModel, 'attribute' => 'main2', 'form' => $form]) ?>

            <div class="row tall">
                <div class="col-md-4">
                    <?= $this->render('_main-pic', ['model' => $mainDisplayModel, 'attribute' => 'pic2']) ?>
                </div>
                <div class="col-md-8">
                    <?= $this->render('_main-sortable', ['model' => $mainDisplayModel, 'attribute' => 'comment2', 'form' => $form]) ?>
                </div>
            </div>

            <div class="tall">
                <?= $this->render('_main-sortable', ['model' => $mainDisplayModel, 'attribute' => 'pr', 'form' => $form]) ?>
            </div>

            <div class="row well tall" style="margin: 0 2px">
                <?php for ($index = 3; $index <= 5; $index++) : ?>
                    <div class="col-md-4">
                        <?= $this->render('_main-pic', ['model' => $mainDisplayModel, 'attribute' => 'pic' . $index]); ?>
                        <?= $this->render('_main-pic', ['model' => $mainDisplayModel, 'attribute' => 'pic' . $index . '_text']); ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-3">
    <h3 style="margin-top: 0"><?= Yii::t('app', '表示することのできる求人原稿項目') ?></h3>
    <p class="mgb20"><?= Yii::t('app', '左レイアウト内の希望の表示箇所に、以下の求人原稿項目をドラッグ＆ドロップしてください。') ?></p>
    <?= $this->render('_sortable', ['items' => $mainDisplayModel->notMainItems, 'name' => 'NotMainItems', 'connected' => 'main-item']); ?>
</div>
<?= $this->render('_submit-button'); ?>
