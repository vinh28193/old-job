<?php

use app\models\manage\ManageMenuMain;
use yii\helpers\Url;
use yii\bootstrap\Html;

/**
 * Created by PhpStorm.
 * User: kai nakamoto
 * Date: 2015/10/23
 * Time: 21:56
 */

$menu = ManageMenuMain::findFromRoute('manage/secure/settings/tool-master/index');
$this->title = $menu->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'サイト設定'), 'url' => ['secure/settings/list']];
$this->params['breadcrumbs'][] = $this->title;;

?>

<div class="tool-master-index">
        <?= Html::tag('h1', Html::icon($menu->icon_key) . Html::encode($this->title), ['class' => 'heading']) ?>

        <div class="col-md-12">
            <div class="jumbotron animated fadeIn">
                <div class="row mgb40">
                    <div class="col-md-12">
                        <div class="col-md-12 text-center mgb20">
                            <h1><?= Yii::t("app", "登録完了") ?></h1>
                            <p><?= isset($count) ? Yii::t("app", "データを{count}件登録しました。", ['count' => $count]) : Yii::t("app", "データを登録しました。") ?></p>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                                <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["index"])?>" role="button"><?= Yii::t("app", "続けて登録する") ?></a></p>
                        </div>
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?=Url::to(["/manage/secure"])?>" role="button"><?= Yii::t("app", "管理画面ホームへ") ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
