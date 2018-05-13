<?php

namespace app\modules\manage\components\validators;

use yii;
use yii\validators\Validator;

/**
 * 重複チェックバリデーター
 *   複数の選択肢を登録しなければならない場合の重複登録防止用に使用する
 *
 *   ■バリデータを使用する上での準備
 *   ・重複チェック対象のinputを全て囲む直近のDOM要素に「.overlap-[カラム名]」のクラスを追加
 *
 */
class OverlapValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = '{label}が重複しています。';
    }

    /**
     * サーバーバリデーション
     *
     * @param \yii\base\Model $model the data model being validated
     * @param array|null $attributes the list of attributes to be validated.
     */
    public function validateAttribute($model, $attribute)
    {
        $message = Yii::t('app', $this->message, ['label' => $model->attributeLabels()[$attribute]]);
        $parentArray = Yii::$app->request->post((new \ReflectionClass($model))->getShortName());
        $dataArray = array_column($parentArray, $attribute);
        $checkData = $model->$attribute;
        $cnt = 0;
        if (!is_array($dataArray)) {
            return;
        }
        foreach ($dataArray as $data)  {
            if ($data === $checkData) {
                $cnt++;
                if ($cnt == 2) {
                    $this->addError($model, $attribute, $message);
                    break;
                }
            }
        }
    }

    /**
     * クライアントバリデーション
     *
     * @param \yii\base\Model $model the data model being validated
     * @param string $attribute the name of the attribute to be validated.
     * @param \yii\web\View $view the view object that is going to be used to render views or view files
     * @return string
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = Yii::t('app', $this->message, ['label' => $model->attributeLabels()[$attribute]]);
        $message = json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return <<<JS
var checkTarget = $('.overlap-{$attribute}').find("input[type='text']");
var targetArray = checkTarget.get();
var index = $(checkTarget).index($('#' + attribute.id));
for (var i = 0; i < targetArray.length && 0 <= index; i++) {
    if (i != index && targetArray[i].value == value) {
        messages.push($message);
        break;
    }
}
JS;
    }
}
