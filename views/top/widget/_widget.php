<?php
//レイアウト内のウィジェットの出力
use yii\helpers\Html;
use app\assets\SlickSliderAsset;

/* @var $this yii\web\View */
/* @var $widget app\models\manage\Widget */
/* @var $widgetData \app\models\manage\WidgetData */
SlickSliderAsset::register($this);

$css = <<<CSS
.slick-dots {
  left: 0;
  right: 0;
  position: static;
}
CSS;
$this->registerCss($css);

?>
<?php
$widgetSliderJs = <<<JS
$(function () {
//setting slick.js
  $(".slider").slick({
    dots: true,
    arrows:true,
    swipe: true,
    autoplay: true,
    autoplaySpeed: 5000,
    lazyLoad: 'progressive',
    responsive: [
      {
        breakpoint: 600,
        settings: {
          dots: true,
          arrows : false
        }
      }
    ]
  });
});
JS;
$this->registerJs($widgetSliderJs);
?>
<?php if (count($widget->widgetData) > 0) : ?>
    <div class="<?= $widget->cssClass ?>">
        <?php if ($widget->is_disp_widget_name) : ?>
            <h2><?= Html::encode($widget->widget_name) ?></h2>
        <?php endif; ?>
        <div class="widget-inner <?= $widget->is_slider == 0 ? '' : ' slider' ?>">
            <?php foreach ((array)$widget->widgetData as $widgetData) : ?>
                <?= $this->render('_widget-data', ['widgetData' => $widgetData]) ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>