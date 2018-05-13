<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/08/31
 * Time: 11:20
 */

namespace app\components;

use app\models\manage\searchkey\Area as AreaModel;
use yii\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class Area
 * @package app\components
 *
 * @property AreaModel[] $models
 * @property AreaModel[] $tenantArea
 * @property AreaModel $nationwideArea
 * @property array $listArray
 * @property AreaModel $firstArea
 */
class Area extends Component
{
    /** @var AreaModel[] Areaのモデル配列 */
    private $_models;

    /** @var AreaModel 全国エリア相当のAreaのモデル */
    private $_nationwideArea;

    /**
     * 有効なエリアのモデルを取得する
     * @return AreaModel[]
     */
    public function getModels()
    {
        if (!$this->_models) {
            $this->_models = AreaModel::find()->where(['valid_chk' => AreaModel::FLAG_VALID])->orderBy('sort')->all();
        }
        return $this->_models;
    }

    /**
     * 1エリア設定にしているかを判定する。
     * @return bool
     */
    public function isOneArea()
    {
        return count($this->models) === 1;
    }

    /**
     * 全国エリアを含む、tenantで有効になっているエリアのidとarea_nameを取得する
     * ワンエリアの場合はそのエリアだけを返す
     * @return AreaModel[]
     */
    public function getTenantArea()
    {
        if ($this->isOneArea()) {
            return $this->models;
        }
        return array_merge([$this->nationwideArea], $this->models);
    }

    /**
     * dropDownやcheckboxで使える配列を生成する
     * @return array
     */
    public function getListArray()
    {
        return ArrayHelper::map($this->tenantArea, 'id', 'area_name');
    }

    /**
     * 全国エリア相当のAreaのモデルを生成する
     * @return AreaModel
     * @throws \yii\base\InvalidConfigException
     */
    public function getNationwideArea()
    {
        if ($this->_nationwideArea === null) {
            $this->_nationwideArea = AreaModel::nationwideArea();
        }
        return $this->_nationwideArea;
    }

    /**
     * sortが一番最初のモデルを返す
     * @return AreaModel
     */
    public function getFirstArea()
    {
        $models = $this->models;
        return array_shift($models);
    }

    /**
     * area_dirが一致するareaのmodelを返す
     * @param $dir
     * @return AreaModel|null
     */
    public function fetchAreaByDir($dir)
    {
        foreach ($this->models as $model) {
            if ($model->area_dir == $dir) {
                return $model;
            }
        }
        return null;
    }
}
