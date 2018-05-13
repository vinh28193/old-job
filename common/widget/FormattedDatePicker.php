<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/09/08
 * Time: 16:53
 */

namespace app\common\widget;

use kartik\widgets\DatePicker;
use Yii;

class FormattedDatePicker extends DatePicker
{
    public function init()
    {
        parent::init();
        if (is_int($this->value)) {
            $this->options['value'] = Yii::$app->formatter->asDate($this->value);
        }
        if ($this->type == self::TYPE_RANGE && $this->attribute2 !== null && $this->value2 === null && $this->hasModel() && is_int($this->model->{$this->attribute2})) {
            $this->options2['value'] = Yii::$app->formatter->asDate($this->model->{$this->attribute2});
        }
    }
}