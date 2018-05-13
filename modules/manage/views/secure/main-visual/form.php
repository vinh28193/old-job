<?php

use app\models\manage\ManageMenuMain;
use app\modules\manage\models\requests\MainVisualForm;
use app\modules\manage\views\SessionFlashAlert;
use proseeds\assets\BootBoxAsset;
use yii\bootstrap\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $forms \app\modules\manage\models\requests\MainVisualForm[] */

$menu = ManageMenuMain::findFromRoute(Yii::$app->requestedRoute);
$this->title = Yii::t('app', 'メインビジュアル設定・編集');
$this->params['breadcrumbs'][] = ['label' => $this->title];

BootBoxAsset::confirmBeforeSubmit($this,
    Yii::t('app', 'メインビジュアルを保存します。よろしいですか？')
);

// スライドショーとバナーで画像フォーム数を制御する
$js = <<<_JS_
$(function () {
    $("form").each(function(){
        visualTypeHide($(this), true);
    });

    $(".type-choice").change(function(){
        var form = $(this).parents('form');
        var start = true;
        var type = $(this).val();
        form.find('.image-form').each(function(){
            if(!start){
                if(type === 'banner'){
                    $(this).hide();
                } else {
                    $(this).show();
                }
            }
            start = false;
        });
    });
    
    function visualTypeHide(form, flag) {
        var start = flag;
        form.find('.image-form').each(function(){
            if(!start){
                if(form.find(".type-choice").val() === 'banner'){
                    $(this).hide();
                } else {
                    $(this).show();
                }
            }
            start = false;
        });
    }
});
_JS_;

$this->registerJs($js, View::POS_READY);
$view = $this;

$areaIds = array_map(function (MainVisualForm $form) {
    return $form->area->id ?? 0;
}, $forms)
?>
<h1 class="heading"><?= Html::icon('picture') ?><?= Html::encode($this->title); ?></h1>
<div id="main-visual-container" class="container" data-areas="<?= implode(',', $areaIds) ?>">
    <div class="row">
        <div class="col-md-12">
            <?= SessionFlashAlert::widget() ?>
            <?= Html::beginTag('p', ['class' => 'alert alert-warning']) ?>
            <?= Yii::t('app', 'トップページで表示するメインビジュアル画像を設定できます。') ?><br>
            <br>
            <?= Yii::t('app', '【表示形式について】') ?><br>
            <?= Html::tag(
                'strong',
                Yii::t('app', 'バナー')
            ) ?>：<?= Yii::t('app', '画像を1枚表示される形式です。') ?><br>
            <?= Html::tag(
                'strong',
                Yii::t('app', 'スライドショー')
            ) ?>：<?= Yii::t('app', '画像を2枚～5枚設定でき、トップページではスライドショー形式で表示されます。') ?><br>
            <br>
            <?= Html::tag('span', Yii::t('app', '※複数のエリアをご利用の場合は、エリア毎に画像を設定してください。'), ['class' => 'text-danger']); ?>
            <?= Html::endTag('p') ?>
            <?= \yii\bootstrap\Tabs::widget([
                'encodeLabels' => false,
                'items' => array_map(function (MainVisualForm $form) use ($view) {
                    return [
                        'label' => ($form->area->area_name ?? '全国') . '&nbsp;&nbsp;&nbsp;' . Html::tag(
                            'span',
                            MainVisualForm::validFlags()[$form->mainVisual->valid_chk ?? MainVisualForm::STATUS_CLOSED],
                            $form->mainVisual->valid_chk == MainVisualForm::STATUS_PUBLIC ?
                                ['class' => 'label label-info'] :
                                ['class' => 'label label-default']
                        ),
                        'headerOptions' => [
                            'id' => 'tab-label-' . ($form->area->id ?? 0),
                        ],
                        'options' => [
                            'id' => 'tab-panel-' . ($form->area->id ?? 0),
                        ],
                        'content' => $view->render('_form', [
                            'model' => $form,
                        ]),
                        'active' => $form->isActive(),
                    ];
                }, array_values($forms)),
            ]) ?>
        </div>
    </div>
</div>
