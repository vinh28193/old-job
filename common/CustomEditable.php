<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/04/25
 * Time: 21:21
 */

namespace app\common;

use app\assets\CustomEditableAsset;
use app\common\Helper\JmUtils;
use dosamigos\editable\Editable;
use dosamigos\editable\EditableAddressAsset;
use dosamigos\editable\EditableComboDateAsset;
use dosamigos\editable\EditableDatePickerAsset;
use dosamigos\editable\EditableDateTimePickerAsset;
use dosamigos\editable\EditableSelect2Asset;
use dosamigos\editable\EditableWysiHtml5Asset;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 * Class CustomEditable
 * @package app\common
 */
class CustomEditable extends Editable
{
    /** @var string characterもしくはnumber */
    public $countType = 'character';
    /** @var string X-Editableのoptionのdsplayに渡すjsメソッド */
    public $display;
    /** @var int 最大値 */
    public $maxLength;
    /** @var string タグの種類 */
    public $tag = 'a';
    /** @var string ヒント */
    public $hint;

    /**
     * 初期化
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->hasModel()
                ? Html::getInputId($this->model, $this->attribute)
                : $this->getId();
        }
        Html::addCssClass($this->options, 'editable');
        Html::addCssStyle($this->options, 'cursor:pointer;');
        $this->clientOptions = [
                'placement' => 'right',
                'onblur' => 'submit',
                'showbuttons' => false,
            ] + $this->clientOptions;

        InputWidget::init();
    }

    /**
     * 実行
     */
    public function run()
    {
        $value = $this->value;
        if ($this->hasModel()) {
            $model = $this->model;
            if ($value !== null) {
                if (is_string($value)) {
                    $show = ArrayHelper::getValue($model, $value);
                } else {
                    $show = call_user_func($value, $model);
                }
            } else {
                $show = ArrayHelper::getValue($model, $this->attribute);
            }
        } else {
            $show = $value;
        }
        echo Html::tag($this->tag, Html::encode($show), $this->options);
        // ヒント文
        if (!JmUtils::isEmpty($this->hint)) {
            echo Html::tag('div', $this->hint, ['class' => 'editableHint hint-block', 'style' => 'display: none;']);
        }
        // 文字数カウント
        if ($this->type == 'text' || $this->type == 'textarea') {
            if ($this->countType == 'character') {
                $maxLengthString = $this->maxLength ? ' / ' . $this->maxLength : '';
                echo Html::tag('div',
                    Html::tag('span', 0) . $maxLengthString . Yii::t('app', ' 文字'),
                    ['class' => 'editableCount label label-primary', 'style' => 'display: none;']
                );
            } elseif ($this->countType == 'number') {
                echo Html::tag('div',
                    $this->maxLength . Yii::t('app', '以内の半角数字'),
                    ['class' => 'editableCount label label-primary', 'style' => 'display: none;']
                );
            }
        }
        $this->registerClientScript();
    }

    /**
     * X-Editable pluginの吐き出しとイベントの紐づけ
     */
    protected function registerClientScript()
    {
        $view = $this->getView();
        $language = ArrayHelper::getValue($this->clientOptions, 'language');

        switch ($this->type) {
            case 'address':
                EditableAddressAsset::register($view);
                break;
            case 'combodate':
                EditableComboDateAsset::register($view);
                break;
            case 'date':
                if ($language) {
                    EditableDatePickerAsset::register(
                        $view
                    )->js[] = 'vendor/js/locales/bootstrap-datetimepicker.' . $language . '.js';
                } else {
                    EditableDatePickerAsset::register($view);
                }
                break;
            case 'datetime':
                if ($language) {
                    EditableDateTimePickerAsset::register(
                        $view
                    )->js[] = 'vendor/js/locales/bootstrap-datetimepicker.' . $language . '.js';
                } else {
                    EditableDateTimePickerAsset::register($view);
                }
                break;
            case 'select2':
                EditableSelect2Asset::register($view);
                break;
            case 'wysihtml5':
                $language = $language ?: 'en-US';
                EditableWysiHtml5Asset::register(
                    $view
                )->js[] = 'vendor/locales/bootstrap-wysihtml5.' . $language . '.js';
                break;
            default:
                CustomEditableAsset::register($view);
        }

        $id = ArrayHelper::remove($this->clientOptions, 'selector', '#' . $this->options['id']);

        // Escape meta-characters in element Id
        // http://api.jquery.com/category/selectors/
        // This actually only needs to be done for dots, since Html::getInputId
        // will enforce word-only characters.
        $id = preg_replace('/([.])/', '\\\\\\\$1', $id);

        if ($this->url) {
            $this->clientOptions['url'] = Url::toRoute($this->url);
        }
        if ($this->display) {
            $this->clientOptions['display'] = '{display}';
        }
        $this->clientOptions['type'] = $this->type;
        $this->clientOptions['mode'] = $this->mode;
        $this->clientOptions['name'] = $this->attribute ?: $this->name;
        $pk = ArrayHelper::getValue(
            $this->clientOptions,
            'pk',
            $this->hasModel() ? $this->model->getPrimaryKey() : null
        );
        $this->clientOptions['pk'] = base64_encode(serialize($pk));
        if ($this->hasModel() && $this->model->isNewRecord) {
            $this->clientOptions['send'] = 'always'; // send to server without pk
        }

        $options = str_replace('"{display}"', $this->display, Json::encode($this->clientOptions));
        $js = "jQuery('$id').editable($options);";
        $view->registerJs($js);

        if (!empty($this->clientEvents)) {
            $js = [];
            foreach ($this->clientEvents as $event => $handler) {
                $js[] = "jQuery('$id').on('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }

    }
}