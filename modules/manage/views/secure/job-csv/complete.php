<?php

use yii\helpers\Url;
use app\models\manage\ManageMenuMain;

/** @var integer $count 登録・変更完了件数*/

$menu = ManageMenuMain::findFromRoute('manage/secure/job-csv/index');
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('secure/job/list'))->title, 'url' => ['secure/job/list']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron animated fadeIn">
                <div class="row mgb40">
                    <div class="col-md-12">
                        <div class="col-md-12 text-center mgb20">
                            <h1><?= Yii::t("app", "登録完了") ?></h1>
                            <p><?= isset($count) ? Yii::t("app", "求人情報を{count}件登録しました。", ['count' => $count]) : Yii::t("app", "求人情報を登録しました。") ?></p>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                                <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["index"])?>" role="button"><?= Yii::t("app", "続けて登録する") ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["secure/job/list"])?>" role="button"><?= Yii::t("app", "求人情報の管理へ") ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-4">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["secure/index"])?>" role="button"><?= Yii::t("app", "管理画面ホームへ") ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
