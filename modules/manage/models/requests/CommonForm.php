<?php

namespace app\modules\manage\models\requests;

use app\models\manage\MainVisual;
use yii\base\Model;

/**
 * Class MainVisualForm
 * @package app\modules\manage\models\requests
 *
 * @property MainVisual $mainVisual
 */
class CommonForm extends Model
{
    /**
     * @var callable
     */
    public $generateInputName;

    /**
     * Input ID を生成して返す
     *
     * @param $key
     * @param $attribute
     * @return string
     */
    public function inputId($key, $attribute)
    {
        return implode('_', [
            $this->formName(),
            $key,
            $attribute,
        ]);
    }

    /**
     * Input Name を生成して返す
     *
     * @param $key
     * @param $attribute
     * @return mixed|string
     */
    public function inputName($key, $attribute)
    {
        if (is_callable($this->generateInputName)) {
            return call_user_func($this->generateInputName, parent::formName(), $key, $attribute);
        }

        return parent::formName();
    }
}
