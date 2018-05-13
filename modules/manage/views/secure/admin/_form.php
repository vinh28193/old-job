<?php

use app\common\widget\FormattedDatePicker;
use app\models\MailSend;
use app\models\manage\BaseColumnSet;
use app\modules\manage\models\Manager;
use app\modules\manage\models\MenuCategory;
use app\models\manage\ClientMaster;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\manage\AdminMaster;
use proseeds\widgets\TableForm;
use proseeds\assets\BootBoxAsset;
use kartik\depdrop\DepDrop;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\manage\AdminMaster */
/* @var $form proseeds\widgets\TableForm */
/* @var $manageMenus */
/* @var $corpList array */
/* @var $clientList array */

BootBoxAsset::confirmBeforeSubmit($this, $model->isNewRecord ? Yii::t("app", "管理者情報を登録してもよろしいですか？") : Yii::t("app", "管理者情報を変更してもよろしいですか？"));

//代理店へのクラス
$corpOption = $model->isNewRecord || $model->role == Manager::OWNER_ADMIN ? 'hidden' : '';
//掲載企業へのクラス
$clientOption = $model->isNewRecord || $model->role == Manager::OWNER_ADMIN || $model->role == Manager::CORP_ADMIN ? 'hidden' : '';

$corp = Manager::CORP_ADMIN;
$client = Manager::CLIENT_ADMIN;

//除外するメニューを権限毎に保存する
$exceptionList = MenuCategory::getExceptionList();

$permittedAdmin = '';
$permittedCorp = '';
$permittedClient = '';
foreach ($exceptionList as $MenuCate) {
    foreach ($MenuCate->items as $exception) {
        switch ($exception->permitted_role) {
            case 'owner_admin':
                $permittedAdmin = $permittedAdmin . '$(\'input[value="' . $exception->exception . '"]\').parent().show();';
                $permittedCorp = $permittedCorp . '$(\'input[value="' . $exception->exception . '"]\').parent().hide();';
                $permittedClient = $permittedClient . '$(\'input[value="' . $exception->exception . '"]\').parent().hide();';
                break;
            case 'corp_admin':
                $permittedAdmin = $permittedAdmin . '$(\'input[value="' . $exception->exception . '"]\').parent().show();';
                $permittedCorp = $permittedCorp . '$(\'input[value="' . $exception->exception . '"]\').parent().show();';
                $permittedClient = $permittedClient . '$(\'input[value="' . $exception->exception . '"]\').parent().hide();';
                break;
            case 'client_admin':
                break;
            default:
                break;
        }
    }
}

$hideJs = <<<JS
var corpAttribute = [];
var clientAttribute = [];
function roleControl(roleName){
    if (roleName == 'owner_admin') {
        if (corpAttribute.length === 0) {
            corpAttribute = $("#form").yiiActiveForm("remove", "adminmaster-corp_master_id");
        }
        if (clientAttribute.length === 0) {
            clientAttribute = $("#form").yiiActiveForm("remove", "adminmaster-client_master_id");
        }
        
        $('#form-corp_master_id-tr').hide();
        $('#form-client_master_id-tr').hide();

        $('#exception-title1').show();
        $('#exception-title2').show();
        $('#exception-title3').show();
        // $('#exception-title4').show();
        $('#exception-title5').show();
        // $('#exception-title6').show();
        $('#exception-title7').show();
        $('#exception-title8').show();
        // $('#exception-title9').show();
        // $('#exception-title10').show();
        $('#exception-title11').show();
        $('#exception-title12').show();
        $permittedAdmin;
    } else if (roleName == 'corp_admin') {
        if (corpAttribute.length !== 0) {
            $("#form").yiiActiveForm("add", corpAttribute);
            corptAttribute = [];
        }
        if (clientAttribute.length === 0) {
            clientAttribute = $("#form").yiiActiveForm("remove", "adminmaster-client_master_id");
        }
        
        $('#form-corp_master_id-tr').show();
        $('#form-client_master_id-tr').hide();

        $('#exception-title1').show();
        $('#exception-title2').hide();
        $('#exception-title3').show();
        // $('#exception-title4').show();
        $('#exception-title5').hide();
        // $('#exception-title6').show();
        $('#exception-title7').hide();
        $('#exception-title8').show();
        // $('#exception-title9').show();
        // $('#exception-title10').show();
        $('#exception-title11').hide();
        $('#exception-title12').hide();
        $permittedCorp;
    } else if (roleName == 'client_admin') {
        if (corpAttribute.length !== 0) {
            $("#form").yiiActiveForm("add", corpAttribute);
            corpAttribute = [];
        }
        if (clientAttribute.length !== 0) {
            $("#form").yiiActiveForm("add", clientAttribute);
            clientAttribute = [];
        }
        $('#form-corp_master_id-tr').show();
        $('#form-client_master_id-tr').show();

        $('#exception-title1').show();
        $('#exception-title2').hide();
        $('#exception-title3').hide();
        // $('#exception-title4').show();
        $('#exception-title5').hide();
        // $('#exception-title6').show();
        $('#exception-title7').hide();
        $('#exception-title8').show();
        // $('#exception-title9').show();
        // $('#exception-title10').show();
        $('#exception-title11').hide();
        $('#exception-title12').hide();
        $permittedClient;
    }
}

$(function(){
    var roleName = $("input[name='AdminMaster[role]']:checked").val();
    roleControl(roleName);
});
$('[name="AdminMaster[role]"]').on('change', function() {
    roleControl(this.value);
});
JS;
$this->registerJs($hideJs);


?>

<div class="corp-master-form">
    <?php
    $tableForm = TableForm::begin([
        'id' => 'form',
        'options' => ['enctype' => 'multipart/form-data'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'validationUrl' => Url::to(['ajax-validation', 'id' => $model->isNewRecord ? '0' : $model->id])
    ]);

    $tableForm->beginTable();
    //種別
    echo $tableForm->row($model, 'role')->radioList(AdminMaster::getRoleList());
    //代理店
    // todo 件数（に応じてtenantテーブル等で切り替えるスイッチ）によって一文字入力必須か否かを切り替えられるように
    echo $tableForm->row($model, 'corp_master_id')->widget(Select2::className(), [
        'model' => $model,
        'attribute' => 'corp_master_id',
        'initValueText' => isset($model->corpMaster) ? $model->corpMaster->corp_name : 'すべて',
        'options' => [
            'placeholder' => Yii::t('app', 'すべて'),
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'language' => ['inputTooShort' => new JsExpression('function () {return "1文字以上入力してください";}'),],
            'ajax' => [
                'url' => Url::to('corp-list'),
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }'),
            ],
        ],
    ]);
    //掲載企業
    echo $tableForm->row($model, 'client_master_id', ['enableAjaxValidation' => true])->widget(DepDrop::className(), [
        'type' => DepDrop::TYPE_DEFAULT,
        'data' => ClientMaster::getDropDownArray(false, AdminMaster::VALID_FLAG, $model->corp_master_id ?: false),
        'pluginOptions' => [
            'depends' => [Html::getInputId($model, 'corp_master_id')],
            'url' => Url::to(['client-list']),
            'placeholder' => Yii::t('app', '--選択してください--'),
        ],
    ]);
    echo $tableForm->breakLine();
    //部署名・担当者名にて1セルに2つのinputを表示する。
    echo $tableForm->field($model, 'fullName')->layout(function () use ($model, $tableForm) {
        echo $tableForm->form($model, 'name_sei', ['inputOptions' => ['class' => 'form-control']])->textInput();
        echo '<br>';
        echo $tableForm->form($model, 'name_mei', ['inputOptions' => ['class' => 'form-control']])->textInput();
    });
    echo $tableForm->breakLine();
    echo $tableForm->row($model, 'login_id', ['enableAjaxValidation' => true])->textInput();
    echo $tableForm->row($model, 'password')->textInput();

    //電話番号
    if (array_key_exists('tel_no', Yii::$app->functionItemSet->admin->items)) {
        echo $tableForm->row($model, 'tel_no')->textInput();
    }

    //メールアドレス
    echo $tableForm->row($model, 'mail_address', ['enableAjaxValidation' => true])->textInput();
    //除外する管理者権限
    echo $tableForm->field($model, 'exceptions')->layout(function () use ($model, $exceptionList, $tableForm) {
        foreach ($exceptionList as $key => $category) {
            /** @var MenuCategory $category */
            echo Html::label($category->title, null, ['id' => 'exception-title' . $category->id, 'style' => 'margin-top: 1em;']);
            echo Html::activeCheckboxList($model, 'exceptions', ArrayHelper::map($category->items, 'exception', 'title'), ['id' => 'exceptions' . $category->id, 'unselect' => null]);
        }
    });
    echo $tableForm->breakLine();
    //以下オプション
    foreach (Yii::$app->functionItemSet->admin->optionItems as $option) {
        switch ($option->data_type) {
            case BaseColumnSet::DATA_TYPE_NUMBER:
            case BaseColumnSet::DATA_TYPE_URL:
            case BaseColumnSet::DATA_TYPE_MAIL:
                echo $tableForm->row($model, $option->column_name)->textInput();
                break;
            case BaseColumnSet::DATA_TYPE_DROP_DOWN:
                echo $tableForm->row($model, $option->column_name)->dropDownList(["" => Yii::t("app", "(未選択)")] + $option->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_CHECK:
                echo $tableForm->row($model, $option->column_name)->checkboxList($option->subsetList);
                break;
            case BaseColumnSet::DATA_TYPE_DATE:
                echo $tableForm->row($model, $option->column_name)->widget(FormattedDatePicker::className());
                break;
            case BaseColumnSet::DATA_TYPE_RADIO:
                echo $tableForm->row($model, $option->column_name)->radioList($option->subsetList);
                break;
            default:
                echo $tableForm->row($model, $option->column_name)->textarea(['rows' => 5]); // todo とりあえずこうするが、入力文字数によって高さを調節するjsを導入
                break;
        }
    }
    //状態
    echo $tableForm->row($model, 'valid_chk')->radioList(AdminMaster::getValidChkList());
    //パスワード送信
    if ($model->isNewRecord) {
        echo $tableForm->head($model, 'sendPass');
        echo $tableForm->cell($model, 'sendPass')->hint(
            Yii::t('app', '通知メール文面は{link}から設定できます', [
                'link' => Html::a(Yii::t('app', 'こちら'), Url::to(['/manage/secure/settings/sendmail/list', 'mailTypeId' => MailSend::TYPE_ADMN_CREATE]), ['target' => '_blank'])
            ])
        )->checkbox(['label' => Yii::t('app', '新規ユーザーに登録通知メールを送信する')]);
    }
    $tableForm->attributes = [];
    $tableForm->field($model, 'role')->begin();
    $tableForm->field($model, 'corp_master_id')->begin();
    $tableForm->field($model, 'client_master_id')->begin();
    $tableForm->field($model, 'name_sei')->begin();
    $tableForm->field($model, 'name_mei')->begin();
    $tableForm->field($model, 'login_id', ['enableAjaxValidation' => true])->begin();
    $tableForm->field($model, 'password')->begin();
    $tableForm->field($model, 'tel_no')->begin();
    $tableForm->field($model, 'mail_address', ['enableAjaxValidation' => true])->begin();
    $tableForm->field($model, 'exceptions')->begin();
    $tableForm->field($model, 'valid_chk')->begin();

    foreach (Yii::$app->functionItemSet->admin->optionItems as $option) {
        $tableForm->field($model, $option->column_name)->begin();
    }

    $tableForm->endTable();
    ?>

    <?= $this->render('/secure/common/_form-buttons.php', [
        'model' => $model
    ]); ?>

    <?php TableForm::end(); ?>
</div>