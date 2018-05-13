<?php
/**
 * Created by PhpStorm.
 * User: Takuya Hosaka
 * Date: 2016/05/02
 * Time: 14:57
 */

namespace app\common;

use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

trait SearchModelTrait
{
    /**
     * 全チェック
     * @var boolean
     */
    public $allCheck;

    /**
     * チェックしたID
     * @var array
     */
    public $selected;

    /**
     * @return array
     */
    public function getCsvSearchRules()
    {
        return [
            ['selected', 'safe'],
            ['allCheck', 'boolean'],
        ];
    }

    /**
     * @param $params
     * @param null $formName
     * @return array
     */
    public function parse($params, $formName = NULL)
    {
        $gridData = (array)json_decode(ArrayHelper::getValue($params, 'gridData'));
        if ($gridData === []) {
            return $params;
        }

        if (!is_numeric(ArrayHelper::getValue($gridData, 'selected.0'))) {
            $compositeKey = [];
            foreach ($gridData['selected'] as $s) {
                $compositeKey[] = json_decode($s)->id;
            }
            unset($gridData['selected']);
            $gridData['selected'] = $compositeKey;
        }
        if ($formName) {
            $params = ArrayHelper::merge($params, [$formName => $gridData]);
        } else {
            $params[$formName] = $gridData;
        }
        return $params;
    }

    /**
     * @param ActiveQuery $query
     */
    public function selected($query)
    {
        if (!$this->isEmpty($this->selected)) {
            if ($this->allCheck == true) {
                $query->andFilterWhere(['not', [self::tableName() . '.id' => $this->selected]]);
            } else {
                $query->andFilterWhere([self::tableName() . '.id' => $this->selected]);
            }
        }
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isEmpty($value)
    {
        return $value === '' || $value === [] || $value === null || is_string($value) && trim($value) === '';
    }
}