<?php

use yii\helpers\Url;
use app\models\manage\ManageMenuMain;

/**
 * Created by PhpStorm.
 * User: kai nakamoto
 * Date: 2015/10/23
 * Time: 21:56
 */

$this->title = Yii::t("app", '応募者情報-完了');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','応募者情報'), 'url' => ['list']];
$this->params['breadcrumbs'][] = Yii::t("app", '完了');

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron animated fadeIn">
                <div class="row mgb40">
                    <div class="col-md-12">
                        <div class="col-md-12 text-center mgb20">
                            <h1><?= $isUpdate ? Yii::t("app", '変更完了') : Yii::t("app", '登録完了') ?></h1>
                            <p><?= $isUpdate ? Yii::t("app", '応募者情報の内容が変更されました。') : Yii::t("app", '応募者情報が登録されました。') ?></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["index"])?>" role="button"><?= Yii::t("app", '応募者一覧へ') ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["secure/"])?>" role="button"><?= Yii::t("app", 'トップページへ戻る') ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>