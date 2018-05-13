<?php

use app\assets\SlickSliderAsset;
use app\common\SearchKey;
use app\components\GoogleAnalytics;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\manage\NameMaster;
use app\models\manage\MainDisp;
use app\models\manage\SocialButton;
use app\models\ToolMaster;
use yii\helpers\Url;

/* @var $jobMasterDisp app\models\JobMasterDisp */
/* @var $this \yii\web\View */
/* @var $isPreview bool */
/* @var $dispJobAccessRecommend app\models\JobAccessRecommend */

SlickSliderAsset::register($this);

$adjustPicJs = <<<JS
$(function () {
//setting slick.js
$(".slickNotSlide").slick({
    slidesToShow: 3,
    centerMode: true,
    responsive: [{
        breakpoint: 767,
        settings: {
            slidesToShow: 1,
            dots: true,
            centerMode: false
        }
    }]
});
});
JS;
$this->registerJs($adjustPicJs);

// キープ件数表示
$this->params['keep'] = true;

Yii::$app->site->toolNo = ToolMaster::TOOLNO_MAP['manuscriptDetai'];
Yii::$app->site->jobMaster = $jobMasterDisp;

//メイン表示項目取得
$this->params['bodyId'] = 'detail';

$mainDispList = MainDisp::items($jobMasterDisp->clientChargePlan->disp_type_id);

//ソーシャルボタンリスト取得
$socialButtonList = SocialButton::findAll(['valid_chk' => SocialButton::VALID]);

$kyujinName = NameMaster::getChangeName('求人');
if ($jobMasterDisp->prefNames) {
    $this->params['breadcrumbs'][] = [
        'label' => Html::encode($jobMasterDisp->prefNames),
        'url' => (($isPreview) ? '#' : $jobMasterDisp->prefSearchUrl),
    ];
}
if ($jobMasterDisp->distNames) {
    $this->params['breadcrumbs'][] = [
        'label' => Html::encode($jobMasterDisp->distNames),
        'url' => (($isPreview) ? '#' : $jobMasterDisp->distSearchUrl),
    ];
}
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', '{corpNameDisp}の{kyujin}詳細', ['corpNameDisp' => Html::encode($jobMasterDisp->corp_name_disp), 'kyujin' => $kyujinName]),
    'url' => (($isPreview) ? '#' : ('/kyujin/' . $jobMasterDisp->job_no)),
];

// 検索キーアイコン取得
/** @var SearchKey $searchKeyComponent */
$searchKeyComponent = Yii::$app->searchKey;
$searchKeyIconContents = $searchKeyComponent->searchKeyIconContents($jobMasterDisp);

// formatter取得
/** @var \app\common\ProseedsFormatter $formatter */
$formatter = Yii::$app->formatter;
?>

<div class="container subcontainer flexcontainer">
    <div class="row">
        <!-- Main Contents =============================================== -->
        <!-- Container =========================== -->
        <div class="col-sm-12">

            <h1 class="resultTitle"><?= Html::encode(Yii::$app->site->toolMaster->h1); ?></h1>

            <?php if (count($socialButtonList) > 0 && !$isPreview) : ?>
                <!--social-->
                <div class="mod-social">
                    <?php foreach ((array)$socialButtonList as $socialButton) : ?>
                        <?php /* @var $socialButton \app\models\manage\SocialButton */ ?>
                        <?= $socialButton->social_script ?>
                    <?php endforeach; ?>
                </div>
                <!--/mod-social-->
            <?php endif; ?>

            <!-- mod-jobDetailBox -->
            <article class="mod-jobDetailBox">
                <!--/mod-jobDetailBox__header-->

                <?php if (ArrayHelper::keyExists('main', $mainDispList) && $jobMasterDisp->{$mainDispList['main']->column_name}) : ?>
                    <div class="mod-excerptBox__header">
                        <?= $this->render('_main-item', ['model' => $jobMasterDisp, 'mainDisps' => $mainDispList, 'mainDispName' => 'main']) ?>
                    </div>
                <?php endif; ?>

                <div class="mod-jobDetailBox__container detailContents__main">

                    <?php
                    if ((ArrayHelper::keyExists('title', $mainDispList) && $jobMasterDisp->{$mainDispList['title']->column_name})
                        || (ArrayHelper::keyExists('title_small', $mainDispList) && $jobMasterDisp->{$mainDispList['title_small']->column_name})
                        || $searchKeyIconContents) : ?>
                        <div class="mod-jobDetailBox__iconBox mod-iconBox">
                            <?php
                            if ($searchKeyIconContents) {
                                echo $this->render('@app/views/common/_searchkey-icons', ['searchKeyIconContents' => $searchKeyIconContents]);
                            }
                            if (ArrayHelper::keyExists('title', $mainDispList) && $jobMasterDisp->{$mainDispList['title']->column_name}) {
                                echo $this->render('_main-item', ['model' => $jobMasterDisp, 'mainDisps' => $mainDispList, 'mainDispName' => 'title']);
                            }
                            if (ArrayHelper::keyExists('title_small', $mainDispList) && $jobMasterDisp->{$mainDispList['title_small']->column_name}) {
                                echo $this->render('_main-item', ['model' => $jobMasterDisp, 'mainDisps' => $mainDispList, 'mainDispName' => 'title_small']);
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <section class="mod-jobDetailBox__excerptBox mod-excerptBox excerptBox-primary">

                        <div class="mod-excerptBox__body">
                            <?php if (ArrayHelper::keyExists('pic1', $mainDispList) && $jobMasterDisp->{$mainDispList['pic1']->column_name}) : ?>
                                <div class="mod-excerptBox__photo imgLiquidNotFill imgLiquid">
                                    <?= Html::img($jobMasterDisp->getJobImagePath(1)) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (ArrayHelper::keyExists('comment', $mainDispList) && $jobMasterDisp->{$mainDispList['comment']->column_name}) : ?>
                                <div class="mod-excerptBox__excerpt">
                                    <?= $this->render('_main-item', ['model' => $jobMasterDisp, 'mainDisps' => $mainDispList, 'mainDispName' => 'comment']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                    <!--/excerptBox-primary-->

                    <section class="mod-jobDetailBox__excerptBox mod-excerptBox excerptBox-secondary">
                        <?php if (ArrayHelper::keyExists('main2', $mainDispList) && $jobMasterDisp->{$mainDispList['main2']->column_name}) : ?>
                            <div class="mod-excerptBox__header">
                                <?= $this->render('_main-item', ['model' => $jobMasterDisp, 'mainDisps' => $mainDispList, 'mainDispName' => 'main2']) ?>
                            </div>
                        <?php endif; ?>

                        <div class="mod-excerptBox__body">
                            <?php if (ArrayHelper::keyExists('pic2', $mainDispList) && $jobMasterDisp->{$mainDispList['pic2']->column_name}) : ?>
                                <div class="mod-excerptBox__photo imgLiquidNotFill imgLiquid">
                                    <?= Html::img($jobMasterDisp->getJobImagePath(2)) ?>
                                </div>
                            <?php endif; ?>

                            <?php if (ArrayHelper::keyExists('comment2', $mainDispList) && $jobMasterDisp->{$mainDispList['comment2']->column_name}) : ?>
                                <div class="mod-excerptBox__excerpt">
                                    <?= $this->render('_main-item', ['model' => $jobMasterDisp, 'mainDisps' => $mainDispList, 'mainDispName' => 'comment2']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>
                    <!--/excerptBox-secondary-->


                    <?php if (ArrayHelper::keyExists('pr', $mainDispList) && $jobMasterDisp->{$mainDispList['pr']->column_name}) : ?>
                        <section class="mod-jobDetailBox__excerptBox mod-excerptBox excerptBox-pr">
                            <?= $this->render('_main-item', ['model' => $jobMasterDisp, 'mainDisps' => $mainDispList, 'mainDispName' => 'pr']) ?>
                        </section>
                        <!--/excerptBox-excerptBox-pr-->
                    <?php endif; ?>
                </div>
                <!--/detailContents__main-->


                <?php if ((ArrayHelper::keyExists('pic3', $mainDispList) && $jobMasterDisp->{$mainDispList['pic3']->column_name})
                    || (ArrayHelper::keyExists('pic4', $mainDispList) && $jobMasterDisp->{$mainDispList['pic4']->column_name})
                    || (ArrayHelper::keyExists('pic5', $mainDispList) && $jobMasterDisp->{$mainDispList['pic5']->column_name})
                    || (ArrayHelper::keyExists('pic3_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic3_text']->column_name})
                    || (ArrayHelper::keyExists('pic4_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic4_text']->column_name})
                    || (ArrayHelper::keyExists('pic5_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic5_text']->column_name})) : ?>
                    <section class="mod-jobDetailBox__flexcontainer">
                        <div class="mod-jobDetailBox__slider">
                            <ul class="mod-slider slickNotSlide">
                                <?php if ((ArrayHelper::keyExists('pic3', $mainDispList) && $jobMasterDisp->{$mainDispList['pic3']->column_name})
                                    || (ArrayHelper::keyExists('pic3_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic3_text']->column_name})) : ?>
                                    <li class="mod-slider__item">
                                        <div class="img">
                                            <?php if (ArrayHelper::keyExists('pic3', $mainDispList) && $jobMasterDisp->{$mainDispList['pic3']->column_name}) {
                                                echo Html::img($jobMasterDisp->getJobImagePath(3), ['class' => 'mod-slider__image']);
                                            } ?>
                                        </div>
                                        <?php if (ArrayHelper::keyExists('pic3_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic3_text']->column_name}) : ?>
                                            <p class="mod-slider__excerpt">
                                                <?= $formatter->asJobView($jobMasterDisp->{$mainDispList['pic3_text']->column_name}) ?>
                                            </p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>

                                <?php if ((ArrayHelper::keyExists('pic4', $mainDispList) && $jobMasterDisp->{$mainDispList['pic4']->column_name})
                                    || (ArrayHelper::keyExists('pic4_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic4_text']->column_name})) : ?>
                                    <li class="mod-slider__item">
                                        <div class="img">
                                            <?php if (ArrayHelper::keyExists('pic4', $mainDispList) && $jobMasterDisp->{$mainDispList['pic4']->column_name}) {
                                                echo Html::img($jobMasterDisp->getJobImagePath(4), ['class' => 'mod-slider__image']);
                                            } ?>
                                        </div>
                                        <?php if (ArrayHelper::keyExists('pic4_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic4_text']->column_name}) : ?>
                                            <p class="mod-slider__excerpt">
                                                <?= $formatter->asJobView($jobMasterDisp->{$mainDispList['pic4_text']->column_name}) ?>
                                            </p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>

                                <?php if ((ArrayHelper::keyExists('pic5', $mainDispList) && $jobMasterDisp->{$mainDispList['pic5']->column_name})
                                    || (ArrayHelper::keyExists('pic5_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic5_text']->column_name})) : ?>
                                    <li class="mod-slider__item">
                                        <div class="img">
                                            <?php if (ArrayHelper::keyExists('pic5', $mainDispList) && $jobMasterDisp->{$mainDispList['pic5']->column_name}) {
                                                echo Html::img($jobMasterDisp->getJobImagePath(5), ['class' => 'mod-slider__image']);
                                            } ?>
                                        </div>
                                        <?php if (ArrayHelper::keyExists('pic5_text', $mainDispList) && $jobMasterDisp->{$mainDispList['pic5_text']->column_name}) : ?>
                                            <p class="mod-slider__excerpt">
                                                <?= $formatter->asJobView($jobMasterDisp->{$mainDispList['pic5_text']->column_name}) ?>
                                            </p>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </section>
                    <!-- /mod-jobDetailBox__flexcontainer -->
                <?php endif; ?>

                <div class="mod-jobDetailBox__container detailContents__btn__01">
                    <?= $this->render('_button-group', ['jobMasterDisp' => $jobMasterDisp, 'isOtherButton' => true, 'isPreview' => $isPreview]) ?>
                </div>

                <!-- table -->
                <div class="mod-jobDetailBox__container detailContents__table">
                    <section class="detail_table">
                        <?= $this->render('/common/_list-disp', ['model' => $jobMasterDisp, 'headerMessage' => Yii::t('app', '募集要項')]) ?>
                    </section>
                    <section class="detail_table">
                        <?= $this->render('/common/_client-disp', ['model' => $jobMasterDisp, 'headerMessage' => Yii::t('app', '企業情報')]) ?>
                    </section>
                    <!-- /table -->
                </div>
                <!-- /detailContents__table -->

                <div class="mod-jobDetailBox__container detailContents__btn__02">
                    <?= $this->render('_button-group', ['jobMasterDisp' => $jobMasterDisp, 'isOtherButton' => true, 'isPreview' => $isPreview]); ?>
                </div>

            </article>
            <!-- /mod-jobDetailBox -->


            <?php
            // 閲覧した求人原稿が5件ないとき、getAccessJobMasters()にnullが紛れ込むため、filterしている。
            $accessJobMasters = $dispJobAccessRecommend ? array_filter($dispJobAccessRecommend->AccessJobMasters) : [];
            //　原稿がないときは、タグ自体を出力しない
            if ($accessJobMasters) {
                echo $this->render('_job_access_recommend', ['accessJobMasters' => $accessJobMasters]);
            }
            ?>


            <!-- mod-jobDtailSideBox
            <div class="mod-jobDtailSubBox subBox">
                2カラム表示の場合はここにサイドカラムが表示されます。
            </div>
            /mod-jobDtailSubBox subBox-->

        </div>
        <!-- /col -->

        <?= $this->render('_tel-modal', ['jobMasterDisp' => $jobMasterDisp]); ?>

        <!-- / Main Contents =============================================== -->
    </div>
</div>