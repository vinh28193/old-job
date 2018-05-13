<?php
/**
 * Created by IntelliJ IDEA.
 * User: ueda
 * Date: 15/10/09
 * Time: 14:40
 */

namespace app\modules\manage\models;


use app\models\manage\ManageMenuCategory;

class MenuCategory extends ManageMenuCategory
{
    public $active;

    /**
     * 項目管理カテゴリ
     * @type int
     */
    const ITEM_MANAGEMENT_CATEGORY_NO = 11;

    /**
     * 検索キーカテゴリ
     * @type int
     */
    const SEARCH_KEY_CATEGORY_NO = 12;

    /**
     * 初期設定カテゴリ
     * @type int
     */
    const DEFAULT_SETTING_CATEGORY_NO = 13;

    /**
     * 初期設定一覧ページに表示するカテゴリ
     *
     * このカテゴリはサイドメニューには表示させない
     * @type array
     */
    const DEFAULT_SETTING_MENU_NUMBERS = [
        self::ITEM_MANAGEMENT_CATEGORY_NO,
        self::SEARCH_KEY_CATEGORY_NO,
        self::DEFAULT_SETTING_CATEGORY_NO,
    ];
}
