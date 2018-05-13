<?php
use yii\web\View;
use app\assets\SlickSliderAsset;
use yii\helpers\Html;
use yii\helpers\StringHelper;

SlickSliderAsset::register($this);

/* @var $accessJobMasters mixed | app\models\JobAccessRecommend */

//レコメンドで使用するjQueryの調整
$jobAccessRecommend = <<< JS
jQuery(function($) {
  //レコメンド用スライドショー
  $('.mod-recommendBox__slider').slick({
    dots: true,
    arrows:true,
    infinite: false,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
    ]
  });
});
JS;
$this->registerJs($jobAccessRecommend);
?>
<div class="mod-recommendBox clearfix">
    <div class="mod-recommendBox__view-together">
        <h3 class="title"><?= Yii::t('app', 'この求人情報を見た人はこれも見ている')?></h3>
        <div class="mod-recommendBox__slider clearfix">
            <?php
            /* @var $accessJobMaster app\models\JobMasterDisp */
            foreach ($accessJobMasters AS $accessJobMaster):
                ?>
                <div class="mod-slider__item">
                    <div class="img">
                        <?php if($accessJobMaster->getJobImagePath(1)): ?>
                        <a href="/kyujin/<?= $accessJobMaster->job_no ?>">
                            <?= Html::img(
                                Html::encode($accessJobMaster->getJobImagePath(1))
                            );
                            ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="detail">
                        <?php if($accessJobMaster->job_pr): ?>
                            <p class="catch">
                                <a href="/kyujin/<?= $accessJobMaster->job_no ?>" style="word-break: break-all;">
                                    <?= StringHelper::truncate($accessJobMaster->job_pr, 30) ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        <?php if($accessJobMaster->work_place): ?><p class="area ellipsis"><?= Html::encode($accessJobMaster->work_place) ?></p><?php endif; ?>
                        <?php if($accessJobMaster->station): ?><p class="station ellipsis"><?= Html::encode($accessJobMaster->station) ?></p><?php endif; ?>
                        <?php if($accessJobMaster->wage_text): ?><p class="wage ellipsis"><?= Html::encode($accessJobMaster->wage_text) ?></p><?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>