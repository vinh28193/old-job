<?php

namespace app\modules\manage\models\forms;

use app\models\FreeContent;

/**
 * 管理画面のフリーコンテンツ編集画面用モデル
 *
 * @property FreeContentElementForm[]|null $elements
 * @property FreeContentElementForm[] $elementModels
 */
class FreeContentForm extends FreeContent
{
    /**
     * ElementFormのrelation
     * @return \yii\db\ActiveQuery
     */
    public function getElements()
    {
        return $this->hasMany(FreeContentElementForm::className(), ['free_content_id' => 'id'])->orderBy('sort');
    }

    /**
     * ElementFormのrelationもしくは新規インスタンスを配列を返す
     * @return array
     */
    public function getElementModels()
    {
        return $this->elements ?: [new FreeContentElementForm()];
    }
}
