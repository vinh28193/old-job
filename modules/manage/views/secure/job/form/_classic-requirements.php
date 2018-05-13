<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2017/04/07
 * Time: 15:57
 */
use app\models\manage\ListDisp;
use app\models\manage\MainDisp;
use proseeds\assets\FlatUIAsset;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $id integer */
/* @var $model app\models\manage\JobMaster */
/* @var $dispTypeId integer */
/* @var $tableForm \proseeds\widgets\TableForm */

FlatUIAsset::register($this);

$mainDisp = MainDisp::items($dispTypeId);
$listDisp = ListDisp::items($dispTypeId);

$picJs = <<<JS
$('.select-img').on('click', function() {
    renderPictureModalColtents(this.id);
});
JS;
$this->registerJs($picJs);

$syncInputJs = <<<JS
$('.classic').each(function() {
    var id = this.id;
    var input;
    if (this.length && this[0].tagName.toLowerCase() === 'div') {
        input = $(this).find('input');
    } else {
        input = $(this);
    }

    input.off();
    input.on('change.yiiActiveForm', function(e) {
        $('form').yiiActiveForm('validateAttribute', id);
    });
    input.on('blur.yiiActiveForm', function(e) {
        $('form').yiiActiveForm('validateAttribute', id);
    });
    input.on('keyup.yiiActiveForm', function(e) {
        $('form').yiiActiveForm('validateAttribute', id);
    });
});
JS;
$this->registerJs($syncInputJs);

$picCss = <<<CSS
img.select-img {
    max-width:100%;
    max-height:300px;
    cursor:pointer;
}
@media (max-width: 767px) {
  img.select-img {
    max-width:100%;
    max-height:240px;
    cursor:pointer;
  }
}
CSS;
$this->registerCss($picCss);

/* @var $mainItems array */
$mainItems = [];

// main
$tableForm->beginTable();
foreach (['main', 'title', 'title_small', 'comment', 'main2', 'comment2', 'pr'] as $displayName) {
    if (isset($mainDisp[$displayName]) && !ArrayHelper::isIn($mainDisp[$displayName]->column_name, $mainItems)) {
        echo $this->render('classic/_classic-input', ['model' => $model, 'tableForm' => $tableForm, 'item' => $mainDisp[$displayName]]);
        $mainItems[] = $mainDisp[$displayName]->column_name;
    }
}
$tableForm->endTable();

// 画像
$tableForm->beginTable();
foreach (range(1, 5) as $picId) {
    echo $this->render('classic/_classic-pic', ['model' => $model, 'tableForm' => $tableForm, 'mainDisp' => $mainDisp, 'picId' => $picId]);
    if ($picId >= 3) {
        echo $this->render('classic/_classic-input', ['model' => $model, 'tableForm' => $tableForm, 'item' => $mainDisp["pic{$picId}_text"] ?? null]);
    }
}
$tableForm->endTable();

// list
$tableForm->beginTable();
foreach ($listDisp as $disp) {
    if (!ArrayHelper::isIn($disp->column_name, $mainItems)) {
        echo $this->render('classic/_classic-input', [
            'model' => $model,
            'tableForm' => $tableForm,
            'item' => $disp,
        ]);
    }
}
$tableForm->endTable();
