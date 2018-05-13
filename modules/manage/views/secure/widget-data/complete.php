<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->title = Yii::t('app', 'ウィジェットデータ - 完了');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'ウィジェットデータ'), 'url' => ['list']];
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
                            <h1><?=Yii::t('app', "作成・編集完了")?></h1>
                            <p><?=Yii::t('app', "ウィジェットデータの登録が完了しました。")?></p>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(["list"]) ?>"
                                  role="button"><?=Yii::t('app', "ウィジェットデータ一覧画面へ")?>
                            </a></p>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12">
                        <p><a class="btn btn-block btn-primary btn-lg" href="<?= Url::to(["/manage/secure/index"]) ?>" role="button"><?=Yii::t('app', "トップページへ戻る")?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>