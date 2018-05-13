<?php

/* @var $this yii\web\View */

use yii\bootstrap\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'アクセス権限がありません');
$this->params['breadcrumbs'][] = Yii::t('app', 'アクセス制限');
?>
<h1 class="heading"><?= $this->title ?></h1>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron animated fadeIn">
                <div class="row mgb40">
                    <div class="col-md-12">
                        <div class="col-md-12 text-center mgb20">
                            <p><?= Yii::t('app', '誠に申し訳ございません。') ?></p>
                            <p><?= Yii::t('app', 'こちらの画面はメニューのアクセス制限により閲覧・操作できません') ?>。</p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><?= Html::a(Yii::t('app', '求職者画面'), Url::to('/'), ['class' => 'btn btn-block btn-primary btn-lg', 'role' => 'button']) ?></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><?= Html::a(Yii::t('app', '管理画面トップ'), Url::to('/manage/secure/'), ['class' => 'btn btn-block btn-primary btn-lg', 'role' => 'button']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>