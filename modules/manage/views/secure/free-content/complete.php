<?php

use app\models\manage\ManageMenuMain;
use yii\helpers\Url;

/* @var $isUpdate */

$this->title = Yii::t('app', 'フリーコンテンツ-完了');
$this->params['breadcrumbs'][] = ['label' => ManageMenuMain::findFromRoute(Url::toRoute('list'))->title, 'url' => ['list']];
$this->params['breadcrumbs'][] = Yii::t('app', '完了');

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron animated fadeIn">
                <div class="row mgb40">
                    <div class="col-md-12">
                        <div class="col-md-12 text-center mgb20">
                            <h1><?= $isUpdate ? Yii::t('app', '変更完了') : Yii::t('app', '登録完了') ?></h1>
                            <p><?= $isUpdate ? Yii::t('app', 'フリーコンテンツが変更されました。') : Yii::t('app', 'フリーコンテンツが登録されました。') ?></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(['index']) ?>" role="button"><?= Yii::t('app', 'フリーコンテンツ一覧へ') ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(['create']) ?>" role="button"><?= Yii::t('app', '新しく追加登録する') ?></a></p>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-12">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(['secure/']) ?>" role="button"><?= Yii::t('app', 'トップページへ戻る') ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
