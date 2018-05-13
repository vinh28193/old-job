<?php
/**
 * Created by PhpStorm.
 * User: proseeds
 * Date: 2016/05/16
 * Time: 17:15
 */

namespace app\common;

use app\models\JobMasterDisp;
use app\models\manage\searchkey\JobSearchkeyItem;
use app\models\manage\searchkey\JobType;
use app\models\manage\SearchkeyMaster;
use yii\helpers\ArrayHelper;

/**
 * Class SearchKey
 * @package app\common
 * @property SearchkeyMaster[] $searchKeys
 * @property array $searchKeyLabels
 * @property SearchkeyMaster[] $iconSearchKeys
 */
class SearchKey extends SearchkeyMaster
{
    private $_searchKeys;

    /**
     * 有効な検索キーのインスタンスを返す
     * @return SearchkeyMaster[]
     */
    public function getSearchKeys()
    {
        if (!$this->_searchKeys) {
            $this->_searchKeys = SearchkeyMaster::findSearchKeys();
        }
        return $this->_searchKeys;
    }

    /**
     * 全ての有効なラベルを返す
     * @return array
     */
    public function getSearchKeyLabels()
    {
        return ArrayHelper::map($this->searchKeys, 'job_relation_table', 'searchkey_name');
    }

    /**
     * 特定のラベルを返す
     * @param $tableName
     * @return mixed
     */
    public function label($tableName)
    {
        return ArrayHelper::getValue($this->searchKeyLabels, $tableName);
    }

    /**
     * アイコン表示する検索キーを返す
     * @return SearchkeyMaster[]
     */
    public function getIconSearchKeys()
    {
        return array_filter($this->searchKeys, function ($searchkey) {
            return $searchkey->icon_flg === self::ICON_FLG_VALID && !ArrayHelper::isIn($searchkey->table_name, self::ICON_STATIC_KEYS);
        });
    }

    /**
     * JobMasterDispモデルを元に検索キーアイコンで表示する文字列の配列を生成する
     * @param JobMasterDisp $jobMasterDisp
     * @return array
     */
    public function searchKeyIconContents(JobMasterDisp $jobMasterDisp)
    {
        $contents = [];
        foreach ($this->iconSearchKeys as $tableName => $searchKey) {
            /** @var SearchkeyMaster $searchKey */
            switch ($tableName) {
                case 'job_type_category':
                    foreach ($jobMasterDisp->{$searchKey->jobRelationName} as $relationModel) {
                        /** @var JobType $relationModel */
                        if (isset($relationModel->jobTypeSmall->job_type_small_name)) {
                            $contents[] = $relationModel->jobTypeSmall->job_type_small_name;
                        }
                    }
                    break;
                default:
                    foreach ($jobMasterDisp->{$searchKey->jobRelationName} as $relationModel) {
                        /** @var JobSearchkeyItem $relationModel */
                        if (
                            // 検索キー項目が存在する
                            $relationModel->searchKeyItem &&
                            // 検索キー項目が有効である
                            $relationModel->searchKeyItem->valid_chk &&
                            // 一階層であるもしくはカテゴリが有効である
                            (strpos($tableName, 'item') !== false || $relationModel->searchKeyItem->category->valid_chk)
                        ) {
                            $contents[] = $relationModel->searchKeyItem->searchkey_item_name;
                        }
                    }
                    break;
            }
        }
        return $contents;
    }
}
