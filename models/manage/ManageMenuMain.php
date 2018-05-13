<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "manage_menu_main".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $manage_menu_main_id
 * @property integer $manage_menu_category_id
 * @property string $title
 * @property string $href
 * @property integer $valid_chk
 * @property integer $sort
 * @property string $icon_key
 * @property string $permitted_role
 * @property string $exception
 *
 * @property array $nameMap
 * @property string $name
 * @property ManageMenuCategory $category
 */
class ManageMenuMain extends BaseModel
{
    /**
     * @var $searchkeyName string
     * SettingsControllerのcreateManageMenuメソッドにて
     * searchkey_master.searchkey_nameを入れている。
     * settings/listにてラベル表示で使われている。
     */
    public $searchkeyName;

    /**
     * @var $searchkeyValidChk integer
     * 同様にsearchkey_masterテーブルのvalid_chkを入れている。
     * settings/listにて公開中かどうかの判定に使われている。
     */
    public $searchkeyValidChk;

    /**
     * @var array item名配列
     */
    private $nameMap = [
        '/manage/secure/job/list' => '求人情報一覧',
        '/manage/secure/job-csv/index' => '求人情報CSV一括登録・更新',
        '/manage/secure/corp/list' => '代理店情報一覧',
        '/manage/secure/client/list' => '掲載企業情報一覧',
        '/manage/secure/admin/list' => '管理者情報一覧',
        '/manage/secure/application/list' => '応募者情報一覧',
        '/manage/secure/widget-data/list' => 'ウィジェットデータ設定・編集',
        '/manage/secure/option-job/list' => '求人原稿項目設定',
        '/manage/secure/option-corp/list' => '代理店項目設定',
        '/manage/secure/option-client/list' => '掲載企業項目設定',
        '/manage/secure/option-admin/list' => '管理者項目設定',
        '/manage/secure/option-application/list' => '応募者項目設定',
        '/manage/secure/jobtype/list' => '職種検索キー',
        '/manage/secure/area/list' => 'エリア検索キー',
        '/manage/secure/prefdist/list' => '地域グループ検索キー',
        '/manage/secure/wage/list' => '給与検索キー',
        '/manage/secure/option-disptype/list' => '掲載タイプ項目設定',
        '/manage/secure/media-upload/list' => '画像一覧',
        '/manage/secure/admin/create' => '管理者の登録',
        '/manage/secure/admin/profile' => 'マイプロフィール編集',
        '/manage/secure/media-upload/create' => '画像のアップロード',
        '/manage/secure/corp/create' => '代理店の登録',
        '/manage/secure/client/create' => '掲載企業の登録',
        '/manage/secure/job/create' => '求人情報の登録',
        '/manage/secure/widget-data/create' => 'ウィジェットデータの登録',
        '/manage/secure/admin/update' => '管理者の編集',
        '/manage/secure/corp/update' => '代理店の編集',
        '/manage/secure/client/update' => '掲載企業の編集',
        '/manage/secure/job/update' => '求人情報の編集',
        '/manage/secure/application/update' => '応募者の編集',
        '/manage/secure/widget-data/update' => 'ウィジェットデータの編集',
        '/manage/secure/settings/sendmail/list' => 'メール設定',
        '/manage/secure/settings/tag/list' => 'タグ設定',
        '/manage/secure/settings/policy/list' => '規約',
        '/manage/secure/searchkey1/list' => 'こだわり',
        '/manage/secure/searchkey2/list' => '業種',
        '/manage/secure/searchkey3/list' => '取得資格',
        '/manage/secure/searchkey4/list' => '施設・環境条件(絞り込み)',
        '/manage/secure/searchkey5/list' => '仕事の特徴',
        '/manage/secure/searchkey6/list' => '待遇条件(絞り込み)',
        '/manage/secure/searchkey7/list' => '検索キー7(2階層)',
        '/manage/secure/searchkey8/list' => '検索キー8(2階層)',
        '/manage/secure/searchkey9/list' => '検索キー9(2階層)',
        '/manage/secure/searchkey10/list' => '検索キー10(2階層)',
        '/manage/secure/searchkey11/list' => '雇用形態',
        '/manage/secure/searchkey12/list' => '期間',
        '/manage/secure/searchkey13/list' => '1階層チェックボックス３',
        '/manage/secure/searchkey14/list' => '1階層チェックボックス４',
        '/manage/secure/searchkey15/list' => '検索キー15(1階層)',
        '/manage/secure/searchkey16/list' => '検索キー16(1階層)',
        '/manage/secure/searchkey17/list' => '検索キー17(1階層)',
        '/manage/secure/searchkey18/list' => '検索キー18(1階層)',
        '/manage/secure/searchkey19/list' => '検索キー19(1階層)',
        '/manage/secure/searchkey20/list' => '検索キー20(1階層)',
        '/manage/secure/settings/tool-master/index' => 'TDK管理',
        '/manage/secure/option-inquiry/list' => '掲載問い合わせ管理',
        '/manage/secure/settings/searchkey/list' => '検索キー設定',
        '/manage/secure/analysis-page/list' => 'アクセス履歴',
        '/manage/secure/analysis-daily/list' => '日別アクセス数集計',
        '/manage/secure/widget/index' => 'トップ画面のレイアウト設定',
        '/manage/secure/settings/header-footer-html/update' => 'ヘッダー・フッター設定',
        '/manage/secure/settings/display/index' => '求人詳細画面表示',
        '/manage/secure/settings/custom-field/list' => 'カスタムフィールド設定',
        '/manage/secure/settings/hot-job/update' => '注目情報設定',
        '/manage/secure/main-visual/form' => 'メインビジュアル設定・編集',
        '/manage/secure/free-content/list' => 'フリーコンテンツ設定・編集',
        '/manage/secure/free-content/create' => 'フリーコンテンツ登録',
        '/manage/secure/free-content/update' => 'フリーコンテンツ更新',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manage_menu_main';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'href'], 'required'],
            [
                [
                    'manage_menu_category_id',
                    'valid_chk',
                    'sort',
                ],
                'integer'
            ],
            [['icon_key', 'title', 'href', 'permitted_role', 'exception'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'manage_menu_category_id' => Yii::t('app', '管理画面大メニューID'),
            'title' => Yii::t('app', 'タイトル'),
            'href' => Yii::t('app', 'URL'),
            'valid_chk' => Yii::t('app', '状態'),
            'sort' => Yii::t('app', '表示順'),
            'corp_available' => Yii::t('app', '代理店閲覧メニュー'),
            'client_available' => Yii::t('app', '掲載企業閲覧メニュー'),
            'icon_key' => Yii::t('app', 'Icon Key'),
        ];
    }

    /**
     * @param $route
     * @return array|null|ManageMenuMain
     */
    public static function findFromRoute($route)
    {
        // 最後の文字が'/'なら'/'を消す
        $route = substr($route, -1) == '/' ? substr($route, 0, -1) : $route;
        // 最初の文字が'/'でなければ'/'を足す
        $route = substr($route, 0, 1) != '/' ? '/' . $route : $route;
        return self::find()->where(
            ['href' => $route]
        )->one();
    }

    /**
     * item名配列を返す
     *
     * @return array
     */
    public function getNameMap(): array
    {
        return $this->nameMap;
    }

    /**
     * リンク先からメニュー名を返す
     *
     * @return string
     */
    public function getName(): string
    {
        return ArrayHelper::getValue($this->nameMap, $this->href, '');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(ManageMenuCategory::className(), ['id' => 'manage_menu_category_id']);
    }
}
