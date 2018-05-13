<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/09
 * Time: 21:26
 */

namespace app\common;


use app\assets\KyujinFormAsset;
use proseeds\widgets\TableForm;
use yii\base\InvalidCallException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\ActiveField;

class KyujinForm extends TableForm
{
    public $options = [
        'enctype' => 'multipart/form-data',
        'class' => 'mod-form1'
    ];
    public $tableOptions = ['class' => 'table mod-table1'];
    public $tableHeaderOptions = [];
    public $tableFieldClass = 'app\common\KyujinField';
    public $fieldConfig = [
        'labelOptions' => [
            'tag' => 'p',
            'class' => 'control-label'
        ]
    ];

    /**
     * @var ActiveField[] the ActiveField objects that are currently active
     * ActiveFormから持ってきたprivate property
     */
    private $_fields = [];

    /**
     * Asset書き換えてinitJs追加
     */
    public function run()
    {
        // ActiveFormの処理
        echo Html::endTag("div");
        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        $content = ob_get_clean();
        echo Html::beginForm($this->action, $this->method, $this->options);
        echo $content;

        if ($this->enableClientScript) {
            $id = $this->options['id'];
            $options = Json::htmlEncode($this->getClientOptions());
            $attributes = Json::htmlEncode($this->attributes);
            $view = $this->getView();
            KyujinFormAsset::register($view); // Asset書き換え
            $view->registerJs("jQuery('#$id').yiiActiveForm($attributes, $options);");
        }

        echo Html::endForm();
        $js = <<<JS
$("#{$this->options['id']}").on("afterValidateAttribute.yiiActiveForm", function(event, attribute, messages){
  if ($(attribute.input).prop("tagName") == "UL") {
    if (messages.length > 0) {
      $(attribute.input).closest('td').addClass("form-requiredItem");
    } else {
      $(attribute.input).closest('td').removeClass("form-requiredItem");
    }
  } else {
    if (messages.length > 0) {
      $(attribute.input).addClass("form-requiredItem");
    } else {
      $(attribute.input).removeClass("form-requiredItem");
    }
  }
});
JS;
        $this->getView()->registerJs($js);
        // initJs追加
        $initJs = <<<JS
$.each($("#{$this->options['id']}").yiiActiveForm("data").attributes, function() {
  if (this.value) {
    $("#{$this->options['id']}").yiiActiveForm("validateAttribute", this.id);
  }
});
JS;
        $this->getView()->registerJs($initJs, View::POS_LOAD);
    }

    /**
     * IDE補完の為
     * @param $model
     * @param $attribute
     * @param array $options
     * @return KyujinField
     */
    public function row($model, $attribute, $options = [])
    {
        return parent::row($model, $attribute, $options);
    }
}