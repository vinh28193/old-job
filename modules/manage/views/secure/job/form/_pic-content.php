<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/04/16
 * Time: 16:40
 */

use app\models\manage\JobPic;
use proseeds\assets\AdminAsset;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this \yii\web\View */
/* @var $jobPic JobPic */

Pjax::begin([
    'enablePushState' => false,
    'formSelector' => '#upload-form',
    'options' => ['id' => 'picContent']
]);
$form = ActiveForm::begin([
    'action' => '/manage/secure/job/upload-pic',
    'method' => 'post',
    'options' => [
        'id' => 'upload-form',
        'enctype' => "multipart/form-data",
        'name' => 'upload'
    ]
]);

$jobInputType = Yii::$app->user->identity->job_input_type;
AdminAsset::register($this);
$inputPicJs = <<<JS
$(".img-responsive").on("click", function(){
    var src = $(this).attr("src");
    var jobInputType = {$jobInputType};
    var target;
    var modelId = $(this).attr("data-model_id");
    // ファイル名のみ保存
    // var fileName = src.substring(src.lastIndexOf('/')+1, src.length);
    // $("#jobmaster-" + PictureColumnNameClicked).val(fileName);
    // フルパス保存
    $("#jobmaster-" + PictureColumnNameClicked).val(modelId);
    $("#picModal").modal('hide');
    if(jobInputType){
        target = $('#' + PictureColumnNameClicked);
    }else{
        target = $('iframe').contents().find('#' + PictureColumnNameClicked);
    }
    
    target.attr("src", src);
});
$("#delete").on("click", function(){
    $("#jobmaster-" + PictureColumnNameClicked).val(null);
    $("#picModal").modal('hide');
    var target = $('iframe').contents().find('#' + PictureColumnNameClicked);
    target.attr("src", "/pict/dummy.jpg");
});
JS;
$this->registerJs($inputPicJs);

$findPicByTagJs = <<<JS
    //show owner tag button
    function getFilterTag(obj) {
        var finder = $(obj).data("finder");
        var filter = $(obj).data("filter");
        var valueSelect = $(finder).val();
        if(valueSelect === ""){
            $(filter).show();
        }else{
            $(filter).hide();
            $(filter + "[data-tag='" + valueSelect + "']").show();
       }
    }
    $('button.filterTagButton').on('click',function(e){
       getFilterTag(this);
   });
JS;
$this->registerJs($findPicByTagJs);


if (Yii::$app->session->hasFlash('message')): ?>
    <div class="jumbotron animated fadeIn text-center">
        <h3><?= Yii::$app->session->getFlash('message') ?></h3>
    </div>
<?php endif; ?>
    <div class="panel panel-info mgb20">
        <div class="panel-heading"><?= Yii::t('app', '一覧へ写真を追加する') ?></div>
        <table class="table">
            <tbody>
            <tr>
                <td>
                    <?= $form->field($jobPic, 'imageFile')->fileInput()->label(false); ?>
                    <?= Html::activeHiddenInput($jobPic, 'client_master_id') ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <p class="search-btn-group text-center">
        <button type="submit" name="imageUpload" class="btn btn-primary" data-pjax="1">
            <?= Html::icon('search') . Yii::t('app', 'アップロード') ?>
        </button>
        <button type="button" class="btn btn-danger" id="delete">
            <?= Html::icon('trash') . Yii::t('app', '削除する') ?>
        </button>
    </p>
<?php ActiveForm::end(); ?>
    <div>
        <div class="row">
            <div class="col-xs-12">
                <h3><?= Yii::t('app', '使用する画像を選択') ?></h3>
            </div>
            <?= $this->render('_pics', [
                'pics' => $jobPic->clientPics,
                'title' => Yii::t('app', 'アップロードした画像'),
                'auth' => 'client',
            ]) ?>
            <?= $this->render('_pics', [
                'pics' => $jobPic->ownerPics,
                'title' => Yii::t('app', 'テンプレート画像'),
                'auth' => 'owner',
            ]) ?>
        </div>
    </div>
<?php Pjax::end() ?>