<?php

/**
 * Created by PhpStorm.
 * User: ryosuke murai
 * Date: 2015/10/20
 * Time: 19:58
 */

namespace app\common;

use app\models\manage\BaseColumnSet;
use app\models\manage\DispType;
use app\models\manage\JobColumnSet;
use app\models\manage\JobMaster;
use Yii;
use yii\base\Component;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\models\manage\ApplicationColumnSet;
use app\models\Apply;

/**
 * Class FunctionItemSet
 * @package app\common
 * @property BaseColumnSet[] $items
 * @property BaseColumnSet[] $optionItems
 * @property BaseColumnSet[] $defaultItems
 * @property BaseColumnSet[] $searchMenuItems
 * @property BaseColumnSet[] $listItems
 *
 * @property array $attributes
 * @property array $attributeLabels
 * @property array $optionAttributes
 * @property array $optionAttributeLabels
 * @property array $searchMenuAttributes
 * @property array $searchMenuAttributeLabels
 * @property BaseColumnSet[] $searchableByKeyWord
 * @property array $listAttributes
 * @property array $listAttributeLabels
 *
 * @property array $rules
 *
 * @property BaseColumnSet[] $shortDisplayItems
 * @property BaseColumnSet[] $searchResultDisplayItems
 * @property BaseColumnSet[] $tagLabels
 * @property DispType[] $dispTypes
 *
 * @property BaseColumnSet[] $applyDispItems
 * @property array $applyItemColumns
 */
class ColumnSet extends Component
{

    const GETTER_ATTRIBUTES = [
        'fullName',
        'fullNameKana',
    ];

    /** @var string */
    public $columnSetModel;

    /**
     * manage_menu_idとvalid_chkを元に取得したレコード情報をitem_columnでindexした配列
     * @var array
     */
    private $_items;

    /**
     * DispTypeのインスタンスの配列
     * @var DispType[]
     */
    private $_dispTypes;

    /**
     * _itemsを返す
     * @return BaseColumnSet[]
     */
    public function getItems()
    {
        if (!isset($this->_items)) {
            /** @var ActiveRecord $columnSetModel */
            $columnSetModel = $this->columnSetModel;
            $query = $columnSetModel::find()->where(['and',
                    [$columnSetModel::tableName() . '.valid_chk' => 1],
                    ['not', [$columnSetModel::tableName() . '.column_name' => '']], // 一時的な処理。最終的にはcolumn_nameには必ず値が入るようにする
            ])->orderBy('column_no');
            // jobの時は両disp tableをwithする
            if ($columnSetModel == 'app\models\manage\JobColumnSet') {
                $query->with(['mainDisps', 'listDisps']);
            }
            // 本来は起こり得ないデータ不整合時の処理
            // 管理画面の検索項目に出ないとかより応募できなくなるとか原稿登録できなくなるとかの方が怖いのでここで除外処理する
            $items = array_filter($query->all(), function ($item) {
                // optionでradioもしくはcheckなのにsubsetが無いものを排除
                return strpos($item->column_name, 'option') === false
                || ($item->data_type != BaseColumnSet::DATA_TYPE_CHECK && $item->data_type != BaseColumnSet::DATA_TYPE_RADIO)
                || $item->subsetItems;
            });

            $this->_items = ArrayHelper::index($items, 'column_name');
        }
        return $this->_items;
    }

    /**
     * オプション項目を取得
     * @return BaseColumnSet[]
     */
    public function getOptionItems()
    {
        return array_filter($this->items, function ($item) {
            return strpos($item->column_name, 'option') !== false;
        });
    }

    /**
     * オプションではない項目を取得
     * @return BaseColumnSet[]
     */
    public function getDefaultItems()
    {
        return array_filter($this->items, function ($item) {
            return strpos($item->column_name, 'option') === false;
        });
    }

    /**
     * 管理画面の一覧画面でのキーワード検索対象項目を取得
     * @return BaseColumnSet[]
     */
    public function getSearchMenuItems()
    {
        return array_filter($this->items, function ($item) {
            return $item['is_in_search'];
        });
    }

    /**
     * 管理画面の一覧画面でのグリッド表示項目を取得
     * @return BaseColumnSet[]
     */
    public function getListItems()
    {
        return array_filter($this->items, function ($item) {
            return $item['is_in_list'];
        });
    }

    /**
     * 各column_nameを取得
     * @return array
     */
    public function getAttributes()
    {
        return ArrayHelper::getColumn($this->items, 'column_name');
    }

    /**
     * [column_name => label]の配列を取得
     * @return array
     */
    public function getAttributeLabels()
    {
        return array_combine(ArrayHelper::getColumn($this->items, 'column_name'), ArrayHelper::getColumn($this->items, 'label'));
    }

    /**
     * オプション項目のcolumn_nameを取得
     * @return array
     */
    public function getOptionAttributes()
    {
        return ArrayHelper::getColumn($this->optionItems, 'column_name');
    }

    /**
     * オプション項目を[column_name => label]の配列の形で取得
     * @return array
     */
    public function getOptionAttributeLabels()
    {
        return array_combine(ArrayHelper::getColumn($this->optionItems, 'column_name'), ArrayHelper::getColumn($this->optionItems, 'label'));
    }

    /**
     * 管理画面の一覧画面でのキーワード検索対象項目のcolumn_nameを取得
     * @return array
     */
    public function getSearchMenuAttributes()
    {
        return ArrayHelper::getColumn($this->searchMenuItems, 'column_name');
    }

    /**
     * 管理画面の一覧画面でのキーワード検索対象項目を[column_name => label]の配列の形で取得
     * @return array
     */
    public function getSearchMenuAttributeLabels()
    {
        return array_combine(ArrayHelper::getColumn($this->searchMenuItems, 'column_name'), ArrayHelper::getColumn($this->searchMenuItems, 'label'));
    }

    /**
     * 管理画面の一覧画面でキーワード検索対象がデフォルトの際に検索されるカラム名（getterで作ったattributeを除く）を取得する
     * @return BaseColumnSet[]
     */
    public function getSearchableByKeyWord()
    {
        return array_filter($this->items, function ($item) {
            return $item['is_in_search'] !== null && !ArrayHelper::isIn($item['column_name'], self::GETTER_ATTRIBUTES);
        });
    }

    /**
     * 管理画面の一覧画面でのグリッド表示項目のcolumn_nameを取得
     * @return array
     */
    public function getListAttributes()
    {
        return ArrayHelper::getColumn($this->listItems, 'column_name');
    }

    /**
     * 管理画面の一覧画面でのグリッド表示項目を[column_name => label]の配列の形で取得
     * todo 使われていないので削除検討
     * @return array
     */
    public function getListAttributeLabels()
    {
        return array_combine(ArrayHelper::getColumn($this->listItems, 'column_name'), ArrayHelper::getColumn($this->listItems, 'label'));
    }

    /**
     * @return array
     */
    public function getRules()
    {
        $rules = [];
//        $date = [];
        $email = [];
        $url = [];
        $required = [];
        $safe = [];

        $getRule = function ($item, $validatorType) {
            if ($item['max_length'] > 0) {
                return [$item['column_name'], $validatorType, 'max' => $item['max_length']];
            } else {
                return [$item['column_name'], $validatorType];
            }
        };

        $items = array_filter($this->items, function ($item) {
            return !strpos($item['column_name'], 'Label');
        });

        foreach ($items as $functionItemId => $item) {
            /** @var BaseColumnSet $item */
            switch ($item['data_type']) {
                case BaseColumnSet::DATA_TYPE_MAIL:
                    $email[] = $item['column_name'];
                    $rules[] = $getRule($item, 'string') + ['max' => 254];
                    break;
                case BaseColumnSet::DATA_TYPE_URL:
                    $url[] = $item['column_name'];
                    $rules[] = $getRule($item, 'string') + ['max' => 2000];
                    break;
                case BaseColumnSet::DATA_TYPE_DATE: // 日付に関しては一旦個別のモデルで設定する形に
//                    $date[] = $item->column_name;
                    break;
                case BaseColumnSet::DATA_TYPE_NUMBER:
                case BaseColumnSet::DATA_TYPE_DROP_DOWN:
                    $rules[] = $getRule($item, 'number');
                    break;
                case BaseColumnSet::DATA_TYPE_TEXT:
                    if ($item->column_name == 'fullName') {
                        $rules[] = ['name_sei', 'string', 'max' => $item->max_length];
                        $rules[] = ['name_mei', 'string', 'max' => $item->max_length];
                        $rules[] = ['fullName', 'string', 'max' => $item->max_length * 2 + 1];
                    } elseif ($item->column_name == 'fullNameKana') {
                        $rules[] = ['kana_sei', 'string', 'max' => $item->max_length];
                        $rules[] = ['kana_mei', 'string', 'max' => $item->max_length];
                        $rules[] = ['fullNameKana', 'string', 'max' => $item->max_length * 2 + 1];
                    } elseif ($item->column_name == 'tel_no' || $item->column_name == 'application_tel_1' || $item->column_name == 'application_tel_2' || $item->column_name == 'fax_no') {
                        $rules[] = [$item->column_name, 'match', 'pattern' => '/^[0-9-]+$/', 'message' => Yii::t('app', '{attribute}は半角数字で入力してください。')]; // todo 文言修正 data_typeにtell欲しい
                        $rules[] = [$item->column_name, 'string', 'max' => $item->max_length];
                    } else {
                        $rules[] = $getRule($item, 'string');
                    }
                    break;
                case BaseColumnSet::DATA_TYPE_RADIO:
                case BaseColumnSet::DATA_TYPE_CHECK:
                default :
                    $safe[] = $item['column_name'];
                    break;
            }
            if ($item['is_must']) {
                // JobMasterの場合、dispType毎に必須項目が変わるのでそれを考慮してシナリオ情報を含めた必須ruleを返す
                // todo ごり押し過ぎるのでDB構造見直しも含めて検討
                if ($this->columnSetModel == JobColumnSet::className()) {
                    if (in_array($item->column_name, JobColumnSet::NOT_REQUIREMENT)) {
                        $required[0][] = $item->column_name;
                    }
                    $dispTypes = ArrayHelper::getColumn(ArrayHelper::getValue($item, 'mainDisps', []), 'disp_type_id');
                    $dispTypes += ArrayHelper::getColumn(ArrayHelper::getValue($item, 'listDisps', []), 'disp_type_id');
                    $dispTypes = array_unique($dispTypes);
                    foreach ($dispTypes as $dispType) {
                        $required[$dispType][] = $item->column_name;
                    }
                } else {
                    // JobMaster以外
                    $required[] = $item['column_name'];
                }
            }
        }

        if ($this->columnSetModel == JobColumnSet::className()) {
            $requiredArray[] = [ArrayHelper::getValue($required, 0, []), 'required'];

            foreach ($this->dispTypes as $key => $dispType) {
                switch ($dispType->disp_type_no) {
                    case 1:
                        $requiredArray[] = [
                            ArrayHelper::getValue($required, $dispType->id, []),
                            'required',
                            'on' => [JobMaster::SCENARIO_DEFAULT, JobMaster::SCENARIO_AJAX_VALIDATION, JobMaster::TYPE_1],
                        ];
                        break;
                    case 2:
                        $requiredArray[] = [
                            ArrayHelper::getValue($required, $dispType->id, []),
                            'required',
                            'on' => [JobMaster::SCENARIO_DEFAULT, JobMaster::SCENARIO_AJAX_VALIDATION, JobMaster::TYPE_2],
                        ];
                        break;
                    case 3:
                        $requiredArray[] = [
                            ArrayHelper::getValue($required, $dispType->id, []),
                            'required',
                            'on' => [JobMaster::SCENARIO_DEFAULT, JobMaster::SCENARIO_AJAX_VALIDATION, JobMaster::TYPE_3],
                        ];
                        break;
                    default:
                        break;
                }
            }
        } else {
            $requiredArray[] = [$required, 'required'];
        }

        return ArrayHelper::merge($rules, [
//            [$date, 'date', 'format' => 'yyyy-M-d'],
            [$email, 'email'],
            [$url, 'url'],
            [$safe, 'string', 'when' => function () {
                return false;
            }],
        ], $requiredArray);
    }

////// jobでのみ使用されるmethod ///////////////////////////////////////////////////////////////////////////////////////
    /**
     * 簡易表示アイテムを取得する
     * @return BaseColumnSet[]
     */
    public function getShortDisplayItems()
    {
        $items = $this->items;
        if (reset($items)->hasAttribute('short_display')) {
            $shortDisplayItems = array_filter($this->items, function ($item) {
                return $item->short_display;
            });
            ArrayHelper::multisort($shortDisplayItems, 'short_display');
            return $shortDisplayItems;
        }
        return [];
    }

    /**
     * 検索結果表示アイテムを取得する
     * @return BaseColumnSet[]
     */
    public function getSearchResultDisplayItems()
    {
        $items = $this->items;
        if (reset($items)->hasAttribute('search_result_display')) {
            $searchResultDisplayItems = array_filter($this->items, function ($item) {
                return $item->search_result_display;
            });
            ArrayHelper::multisort($searchResultDisplayItems, 'search_result_display');
            return $searchResultDisplayItems;
        }
        return [];
    }

    /**
     * タグ変換変数のラベルの配列を取得
     * @return BaseColumnSet[]
     */
    public function getTagLabels()
    {
        return array_filter($this->items, function ($v) {
            return in_array($v->column_name, Site::TAG_CONVERSION_MAP);
        });
    }

    /**
     * @return \app\models\manage\DispType[]
     */
    function getDispTypes()
    {
        if (!$this->_dispTypes) {
            $this->_dispTypes = DispType::find()->all();
        }
        return $this->_dispTypes;
    }

////// applicationでのみ使用されるmethod ///////////////////////////////////////////////////////////////////////////////
    /**
     * 応募画面用items
     * @return BaseColumnSet[]
     */
    public function getApplyDispItems()
    {
        //ApplicationColumnSet以外の場合、itemはそのまま返す。
        if ($this->columnSetModel !== ApplicationColumnSet::className()) {
            return $this->items;
        }

        // array_filterを使うか？foreachでremoveするか？
        return array_filter($this->items, function ($item) {
            /** @var ApplicationColumnSet $item */
            return !in_array($item->column_name, ApplicationColumnSet::ITEMS_NOT_REGISTERED);
        });

//        $items = $this->items;
//        foreach ((array)ApplicationColumnSet::ITEMS_NOT_REGISTERED as $removeItems) {
//            ArrayHelper::remove($items, $removeItems);
//        }
//        return $items;
    }

    /**
     * 応募確認画面用itemsで使うDetailView用の配列を返す。
     * @return array
     */
    public function getApplyItemColumns()
    {
        if ($this->columnSetModel !== ApplicationColumnSet::className()) {
            return ArrayHelper::getColumn($this->applyDispItems, 'column_name');
        }
        return ArrayHelper::getColumn($this->applyDispItems, function ($columnSet) {
            return ArrayHelper::getValue(Apply::DETAIL_VIEW_FIELDS, $columnSet->column_name, $columnSet->column_name);
        });
    }
}
