<?php

use yii\helpers\Html;
use app\assets\SlickSliderAsset;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $mainVisual \app\models\MainVisual */

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
  $(".main-visual-slider").slick({
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
<!--▼ MainVisual ▼-->
<div class="widgetlayout widgetlayout1">
    <div class="widget main-visual <?= $mainVisual->isSlider() ? '' : 'box-pc-1 box-sp-1 style-pc-1 style-sp-1' ?>">
        <div class="widget-inner <?= $mainVisual->isSlider() ? 'main-visual-slider' : '' ?>">
            <?php foreach ($mainVisual->images as $image): ?>
                <?php $imageTag = Html::tag(
                    'span',
                    Html::tag(
                        'img',
                        '',
                        [
                            ($mainVisual->isSlider() ? 'data-lazy' : 'src') => $image->imageUrl,
                            'alt' => $image->content,
                        ]
                    ),
                    [
                        'class' => 'img',
                    ]
                ); ?>
                <?= Html::beginTag('div', ['class' => 'widget-data']) ?>
                <?php if ($image->linkUrl): ?>
                    <?= Html::a(
                        $imageTag,
                        $image->linkUrl
                    ) ?>
                <?php else: ?>
                    <?= $imageTag ?>
                <?php endif; ?>
                <?= Html::endTag('div') ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!--▲ MainVisual ▲-->