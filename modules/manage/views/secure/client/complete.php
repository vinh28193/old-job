<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = Yii::t('app', '掲載企業情報-完了');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '掲載企業情報'), 'url' => ['list']];
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
                            <h1><?= $isUpdate ? Yii::t('app', "変更完了") : Yii::t('app', "登録完了") ?></h1>
                            <p><?= $isUpdate ? Yii::t('app', "掲載企業情報の内容が変更されました。") : Yii::t('app', "掲載企業情報が登録されました。") ?></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["index"])?>" role="button"><?= Yii::t('app', '掲載企業情報一覧へ') ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["create"])?>" role="button"><?= Yii::t('app', '新しく追加登録する') ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-12"">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["secure/"])?>" role="button"><?= Yii::t('app', 'トップページへ戻る') ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
