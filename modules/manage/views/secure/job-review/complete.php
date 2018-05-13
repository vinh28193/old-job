<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = $isRequest ? Yii::t('app', '審査依頼 - 完了') : Yii::t('app', '審査完了');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '求人原稿情報'), 'url' => Url::to(['secure/job/index'])];
$this->params['breadcrumbs'][] = Yii::t('app', '完了');
?>

<h1 class="heading"><span class="glyphicon glyphicon-list-alt"></span><?= Html::encode($this->title) ?></h1>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron animated fadeIn">
                <div class="row mgb40">
                    <div class="col-md-12">
                        <div class="col-md-12 text-center mgb20">
                            <h1><?= $isRequest ? Yii::t('app', '審査依頼完了') : Yii::t('app', '審査完了') ?></h1>
                            <p><?= $isRequest ? Yii::t('app', '審査依頼が完了しました。') : Yii::t('app', '審査が完了しました。') ?></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(['secure/job/index']) ?>" role="button"><?= Yii::t('app', '求人原稿情報一覧へ') ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(['secure/']) ?>" role="button"><?= Yii::t('app', 'トップページへ戻る') ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
