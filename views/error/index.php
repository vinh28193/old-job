<?php

use yii\helpers\Html;
use app\models\forms\JobSearchForm;
use yii\widgets\ActiveForm;

$this->params['bodyId'] = 'page404';
$searchForm             = new JobSearchForm();
?>

<!-- Main Contents =============================================== -->

<!-- Container =========================== -->
<div class="container subcontainer flexcontainer">
    <div class="row">
        <div class="col-sm-12">

            <div class="mod-subbox-wrap">
                <h1 class="mod-h1">404 File not found.</h1>
                <div class="mod-subbox">
                    <h1><?= Yii::t('app', '残念ですが、お探しのページは見つかりませんでした') ?></h1>
                    <p><?= Yii::t('app', '該当するお仕事情報が見つかりません。') ?></p>
                    <p><?= Yii::t('app', '掲載が終了した可能性があります。') ?></p>
                    <p><?= Yii::t('app', 'お手数ですが、トップ、または下記のリンク、検索フォームより再度お探しください。') ?></p>
                    <ul class="list-inline">
                        <li><span class="fa fa-arrow-right"></span><a href="/top"><?= Yii::t('app', 'トップページ') ?></a></li>
                        <?php //TODO:ルーティングが上手く行かないので一旦ベタ打ち ?>
                        <li><span class="fa fa-arrow-right"></span><a href="/kyujin/search-result"><?= Yii::t('app', '求人を探す') ?></a></li>
                    </ul>
                    <?php $form = ActiveForm::begin([
                        'id'      => 'search-form',
                        'action'  => '/kyujin/search-result',
                        'method'  => 'post',
                        'options' => [
                            'class' => 'row',
                        ],
                    ]);

                    echo '<div class="row">';
                    echo '<div class="search-field col-sm-6">';

                    echo Html::activeTextInput($searchForm, 'keyword', ['placeholder' => 'キーワードを入力', 'class' => 'text-field',]);
                    echo Html::a('<span class="fa fa-search"></span> ', 'Javascript:void(0)',
                        ['onclick' => '$(\'#search-form\').submit();return false;']);

                    echo '</div>';
                    echo '</div>';

                    ActiveForm::end(); ?>
                </div>
            </div>

        </div>
        <!-- / Main Contents =============================================== -->
    </div>
</div>