<?php

namespace app\modules\manage\controllers\secure;

use app\modules\manage\models\Manager;
use Yii;
use app\modules\manage\controllers\CommonController;
use app\common\AccessControl;
use app\modules\manage\models\MenuCategory as MC;
use app\models\manage\ManageMenuMain;
use app\models\manage\SearchkeyMaster;
use yii\helpers\ArrayHelper;

/**
 * 初期設定一覧コントローラ
 *
 * @uses CommonController
 * @package
 */
class SettingsController extends CommonController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list'],
                        'roles' => ['owner_admin'],
                    ],
                ],
            ],
        ]);
    }

    /**
     * 一覧画面表示
     *
     * @return mixed
     */
    public function actionList()
    {
        $menus = [];
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;

        foreach ($identity->myMenu as $menu) {
            $no = $menu->manage_menu_category_no;
            if (!in_array($no, MC::DEFAULT_SETTING_MENU_NUMBERS)) {
                continue;
            }

            if ($no == MC::SEARCH_KEY_CATEGORY_NO) {
                $menu->populateRelation('items', $this->getSearchKeyItems());
            } else {
                $menu->populateRelation('items', $menu->items);
            }

            $menus[] = $menu;
        }

        return $this->render('list', [
            'settingMenus' => $menus,
        ]);
    }

    /**
     * 検索キーに表示する項目を取得する
     *
     * @return array
     */
    private function getSearchKeyItems()
    {
        $searchkeyMaster = new SearchkeyMaster();
        $searchKeyItems = $searchkeyMaster->getSettingMenus();
        $items = ArrayHelper::getColumn($searchKeyItems, function ($item) {
            return $this->createManageMenu($item);
        });

        return $items;
    }

    /**
     * SearchkeyMasterオブジェクトからManageMenuMainオブジェクトを作成する
     *
     * @param \app\models\manage\SearchkeyMaster $searchkey
     * @return \app\models\manage\ManageMenuMain
     */
    private function createManageMenu($searchkey)
    {
        $menu = new ManageMenuMain();
        $menu->searchkeyName = $searchkey->searchkey_name;
        $menu->href = $this->makeSearchkeyUrl($searchkey->table_name);
        $menu->icon_key = 'search';
        $menu->searchkeyValidChk = $searchkey->valid_chk;

        return $menu;
    }

    /**
     * 検索キー項目のURLを作成する
     *
     * searchkey_master.table_nameの値から、検索キー項目のurlを生成
     *
     * 生成ルール
     * 1. ['_category', '_master', '_item'] 左記いずれかの文字列をtable_nameから削除
     * 2. '_' をtable_nameからすべて削除
     *
     * @param string $tableName テーブル名 searchkey_master.table_name
     * @return string
     */
    private function makeSearchkeyUrl($tableName)
    {
        $fmt = '/manage/secure/%s/list';

        // 削除する文字列
        $search = [
            '_category',
            '_master',
            '_item',
        ];
        $str = str_replace($search, '', $tableName);
        // '_' は最後に置換
        $str = str_replace('_', '', $str);

        return sprintf($fmt, $str);
    }
}


