<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dosamigos\ckeditor\CKEditor;
use proseeds\widgets\TableForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\manage\Policy */
$this->title = Yii::t('app', '項目変更');
?>

<?php
$tableForm = TableForm::begin([
    'options' => [
        'id' => 'policy_form',
        'enctype' => 'multipart/form-data'
    ],
    'tableOptions' => [
        'class' => 'table table-bordered'
    ],
]);

?>

<?php
$tableForm->beginTable();
echo $tableForm->row($model, 'policy_name')->textInput();

if ($model->from_type == $model::FROM_TYPE_MEMBER) {

    echo $tableForm->row($model, 'valid_chk')->radioList($model->getValidChkLabel());

} else {

    echo $tableForm->row($model, 'valid_chk')->text();

}

// todo 複数使うようならボタンを共通化。そもそもライセンスの問題どうするか？
echo $tableForm->row($model, 'policy', [
    'template' => "<td colspan='2'>{input}\n{hint}\n{error}\n</td>",
])->widget(CKEditor::className(), [
    'options' => ['rows' => 6],
    'preset' => 'custom',
    'clientOptions' => [
        'filebrowserUploadUrl' => Url::to('/manage/secure/settings/policy/upload'),
        'toolbarGroups' => [
            ['name' => 'clipboard', 'groups' => ['mode', 'undo', 'selection', 'clipboard']],
            ['name' => 'editing', 'groups' => ['tools']],
            ['name' => 'paragraph', 'groups' => ['templates', 'list', 'align']],
            ['name' => 'insert'],
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'colors'],
            ['name' => 'links'],
            ['name' => 'others'],
        ],
        'removeButtons' => 'Smiley,Iframe,Image,Flash,Table,PageBreak,Subscript,Superscript,Anchor'
    ],
]);
$tableForm->endTable();
?>

<div class="row mgt20">
    <p class="text-center">
        <?= Html::button(Yii::t('app', "プレビュー"), ['class' => 'btn btn-inverse', 'onclick' => 'newWindowOpen()']) ?>
        <?= Html::submitButton(Yii::t('app', "変更"), ['class' => 'btn btn-primary', 'name' => 'complete']) ?>
    </p>
</div>
<?php TableForm::end(); ?>
<?php
$id = $model->id;
$script = <<<JS
	function newWindowOpen() {
        var target = 'preview_policy';
        var url = "/manage/secure/settings/policy/preview-form?id={$id}";
        window.open(url,target,"width=1024, height=900, scrollbars=yes");
        var policy_form = $('#policy_form');
        var param = policy_form.serializeArray();
        var data = [];
        $.each(param,  function(){
            if(this['value'] != ''){
                data[this['name']] = this['value'];
            }            
        });
        var form = document.createElement("form");
        form.setAttribute("method", "post");
        form.setAttribute("action", url); 
        form.setAttribute("target", target);
        form.style.display = "none"; 
        buildInput(form, data);
        document.body.appendChild(form);
        form.submit();
    }
    
    function buildInput(form, data, parentKey)
    {
        for(var param in data) {
            if (typeof data[param] === 'string') {
                var input = document.createElement("input");
                input.setAttribute("type", "hidden");
                if (typeof parentKey === 'undefined') {
                    input.setAttribute("name", param);
                } else {
                    input.setAttribute("name", parentKey + '[' + param + ']');
                }
                input.setAttribute("value", data[param]);
                form.appendChild(input);
            } else {
                if (typeof parentKey === 'undefined') {
                    buildInput(form, data[param], param);
                } else {
                    buildInput(form, data[param], parentKey + '[' + param + ']');
                }
            }
        }
    }
JS;
$this->registerJs($script, \yii\web\View::POS_END);
?>
