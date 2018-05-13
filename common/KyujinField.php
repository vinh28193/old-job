<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/09
 * Time: 16:15
 */

namespace app\common;


use app\models\Apply;
use proseeds\widgets\PopoverWidget;
use proseeds\widgets\TableField;
use Yii;
use yii\bootstrap\Html;

class KyujinField extends TableField
{
    // 初期化し直し
    public $options = [];
    public $inputOptions = ['class' => 'form-control input-txt'];
    public $drawFeedBackSpan = false;
    public $requiredTagOptions = ['class' => 'mod-label mod-label-required'];
    public $tagOptions = ['class' => 'mod-label mod-label-any'];
    /** @var bool 親がprivateなため作り直した */
    protected $_breakLine = false;

    /**
     * text inputを所定のクラスを付けてrenderingする
     * @param array $options
     * @return $this
     */
    public function textInput($options = [])
    {
        KyujinHtml::addCssClass($this->inputOptions, 'input-txt-large');
        $this->addRequiredClass($options);
        return parent::textInput($options);
    }

    /**
     * 1つのcellに2つのtext inputを表示する
     * @param $attribute1
     * @param $attribute2
     * @param array $options
     * @return $this
     */
    public function pairTextInput($attribute1, $attribute2, $options = [], $options2 = [])
    {

        $fieldObj1 = $this->form->labelForm($this->model, $attribute1);
        $fieldObj2 = $this->form->labelForm($this->model, $attribute2);
        $inputId1 = $fieldObj1->getInputId();
        $inputId2 = $fieldObj2->getInputId();
        $inputIdParent = $this->getInputId();

        //options2が存在する場合、field1とfield2それぞれにoptions1、options2を設定する
        if (isset($options2)) {
            $field1 = $this->textInputList($fieldObj1, $options);
            $field2 = $this->textInputList($fieldObj2, $options2);
        } else {
            $field1 = $this->textInputList($fieldObj1, $options);
            $field2 = $this->textInputList($fieldObj2, $options);
        }
        $this->parts['{input}'] = Html::tag('ul', $field1 . $field2, ['class' => 'mod-form1 inline-text ' . $inputIdParent]);

        $kanaPlugin = <<<JS
$("#{$this->form->options['id']}").on('afterValidateAttribute', function(event, attribute, messages) {
  if (attribute.name == "{$attribute1}") {
    {$this->attribute}LabelClass(messages, $("#{$inputId2}").closest("div"));
  } else if (attribute.name == '{$attribute2}') {
    {$this->attribute}LabelClass(messages, $("#{$inputId1}").closest("div"));
  }
});

var {$this->attribute}LabelClass = function(messages, pareDiv) {
  if (messages.length > 0 || pareDiv.hasClass("has-error") || (pareDiv.hasClass("required") && !pareDiv.hasClass("has-success"))) {
    $(".field-{$inputIdParent}").removeClass("has-success");
    $(".field-{$inputIdParent}").addClass("has-error");
  } else {
    $(".field-{$inputIdParent}").removeClass("has-error");
    $(".field-{$inputIdParent}").addClass("has-success");
  }
};
JS;
        $this->form->view->registerJs($kanaPlugin);
        return $this;
    }

    /**
     * pairTextInputのリストを出力する
     * 結構決め打ちですので、不便そうならformConfigとか渡せるように改修してください
     * @param KyujinField $fieldObj
     * @param $options
     * @return string
     */
    private function textInputList($fieldObj, $options)
    {

        $fieldObj->labelOptions = [
            'tag' => 'label',
            'class' => 'control-label'
        ];
        return Html::tag('li', $fieldObj->textInput($options), ['class' => 'field-' . $fieldObj->getInputId()]);
    }

    /**
     * テキストエリアに所定のクラスを付けてrenderingする
     * @param array $options
     * @return $this
     */
    public function textarea($options = [])
    {
        KyujinHtml::addCssClass($this->inputOptions, 'input-txt-large');
        $this->addRequiredClass($options);
        return parent::textarea($options);
    }

    /**
     * チェックボックスリストを所定のクラスを付けてrenderingする
     * @param array $items
     * @param array $options
     * @return $this
     */
    public function checkboxList($items, $options = [])
    {
        if ($this->model->isAttributeRequired(KyujinHtml::getAttributeName($this->attribute))) {
            KyujinHtml::addCssClass($this->tableDataOptions, 'form-requiredItem');
        }
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = KyujinHtml::activeCheckboxList($this->model, $this->attribute, $items, $options);

        return $this;
    }

    /**
     * ラジオボタンリストを所定のクラスを付けてrenderingする
     * @param array $items
     * @param array $options
     * @return $this
     */
    public function radioList($items, $options = [])
    {
        if ($this->model->isAttributeRequired(KyujinHtml::getAttributeName($this->attribute))) {
            KyujinHtml::addCssClass($this->tableDataOptions, 'form-requiredItem');
        }
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = KyujinHtml::activeRadioList($this->model, $this->attribute, $items, $options);

        return $this;
    }

    /**
     * dropDownListを所定のクラスを付けてrenderingする
     * @param array $items
     * @param array $options
     * @return $this
     */
    public function dropDownList($items, $options = [])
    {
        $this->addRequiredClass($options);
        return parent::dropDownList($items, $options);
    }

    /**
     * @param string $class
     * @param array $config
     * @return $this
     */
    public function widget($class, $config = [])
    {
        if ($this->model->isAttributeRequired(KyujinHtml::getAttributeName($this->attribute))) {
            KyujinHtml::addCssClass($config['options'], 'form-requiredItem');
        }
        return parent::widget($class, $config);
    }

    /**
     * 項目が必須項目の際に必須クラスを付ける（必須クラスはオーバーライド不可）
     * @param $options
     */
    private function addRequiredClass(&$options)
    {
        if ($this->model->isAttributeRequired(KyujinHtml::getAttributeName($this->attribute))) {
            if (isset($options['class'])) {
                KyujinHtml::addCssClass($options, 'form-requiredItem');
            } else {
                KyujinHtml::addCssClass($this->inputOptions, 'form-requiredItem');
            }
        }
    }

    /**
     * 使うHtmlHelperをKyujinHtmlに改変
     * @inheritdoc
     */
    public function render($content = null)
    {
        if ($content === null) {
            if (!isset($this->parts['{input}'])) {
                $this->parts['{input}'] = KyujinHtml::activeTextInput($this->model, $this->attribute, $this->inputOptions);
            }
            if ($this->drawFeedBackSpan === true) {
                $this->parts['{input}'] .= KyujinHtml::tag("span", "", $this->validateMarkOptions);
            }
            if (isset($this->inputWrapper)) {
                $this->parts['{input}'] = KyujinHtml::tag('div', $this->parts['{input}'], $this->inputWrapper);
            }

            if (!isset($this->parts['{label}'])) {
                $this->parts['{label}'] = KyujinHtml::activeLabel($this->model, $this->attribute, $this->labelOptions);
                if ($this->drawRequireLabel) {
                    if (!isset($this->isRequired)) {
                        $this->isRequired = $this->model->isAttributeRequired($this->attribute);
                    }
                    if ($this->isRequired) {
                        $this->parts['{label}'] .= KyujinHtml::tag('span', $this->requiredTagName, $this->requiredTagOptions);
                    } else {
                        $this->parts['{label}'] .= KyujinHtml::tag('span', $this->tagName, $this->tagOptions);
                    }
                }
            }
            if (isset($this->labelWrapper)) {
                $this->parts['{label}'] = KyujinHtml::tag('div', $this->parts['{label}'], $this->labelWrapper);
            }

            if (!isset($this->parts['{error}'])) {
                $this->parts['{error}'] = KyujinHtml::error($this->model, $this->attribute, $this->errorOptions);
            }
            if (!isset($this->parts['{hint}'])) {
                $this->parts['{hint}'] = '';
            }
            if (!isset($this->parts['{td}'])) {
                $this->parts['{td}'] = KyujinHtml::beginTag('td', $this->tableDataOptions) . "\n" . $this->begin();
            }
            if (!isset($this->parts['{/td}'])) {
                $this->parts['{/td}'] = $this->end() . "\n" . KyujinHtml::endTag('td');
            }
            if (!isset($this->parts['{th}'])) {
                $this->parts['{th}'] = KyujinHtml::beginTag('th', $this->tableHeaderOptions) . "\n" . $this->beginHeader();
            }
            if (!isset($this->parts['{/th}'])) {
                $this->parts['{/th}'] = '';
                if ($this->hint !== '') {
                    $this->parts['{/th}'] = PopoverWidget::widget([
                        'dataContent' => $this->hint,
                        'dataHtml' => true
                    ]);
                }
                $this->parts['{/th}'] .= $this->endHeader() . "\n" . KyujinHtml::endTag('th');
            }

            $content = strtr($this->template, $this->parts);
        } elseif (!is_string($content)) {
            $content = call_user_func($content, $this);
        }

        if ($this->_breakLine) {
            $content .= $this->form->breakLine();
        }

        //{td}や{th}を使わない場合は、描画内容を begin(), end()で囲む
        if (strpos($this->template, '{td}') === false && strpos($this->template, '{th}') === false) {
            return $this->begin() . $content . $this->end();
        } else {
            return $content;
        }
    }

    /**
     * 改行する
     * 親の_breakLineがprivateだったため書き直した
     * @return $this
     */
    public function breakLine()
    {
        $this->_breakLine = true;

        return $this;
    }

    /**
     * @param array $options
     * @param null $format
     * @return $this
     */
    public function textWithHiddenInput($options = [], $format = null)
    {
        $model = $this->model;
        $record = $model->formatAsView();

        if (isset($record[$this->attribute])) {
            if (is_array($record[$this->attribute])) {
                $this->parts['{input}'] = $model->subsetString($this->attribute);
            } else {
                if ($format) {
                    $record[$this->attribute] = Yii::$app->formatter->format($record[$this->attribute], $format);
                }
                $this->hiddenInput($options);
                $this->parts['{input}'] .= Html::encode($record[$this->attribute]);
            }
        } else {
            $this->parts['{input}'] = "";
        }

        return $this;
    }
}