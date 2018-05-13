<?php

namespace app\models\manage;

use app\modules\manage\models\Manager;
use app\modules\manage\models\MenuMain;
use proseeds\models\BaseModel;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\rbac\DbManager;
use yii\rbac\Item;

/**
 * This is the model class for table "manage_menu_category".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $title
 * @property integer $sort
 * @property string $icon_key
 * @property integer $valid_chk
 *
 * @property ManageMenuMain[] $items
 *
 * @property array $nameMap
 * @property string $name
 */
class ManageMenuCategory extends BaseModel
{
    /**
     * @var array メニュー名配列
     */
    private $nameMap = [
        'list-alt' => '求人原稿',
        'star' => '代理店',
        'star-empty' => '掲載企業',
        'link' => 'コンテンツ設定',
        'picture' => 'ギャラリー',
        'flag' => '管理者',
        'user' => '応募者',
        'pencil' => '項目管理',
        'search' => '検索キー',
        'wrench' => '初期設定',
        'signal' => 'アクセス解析',
    ];

    const VALID_FLAG = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manage_menu_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'title'], 'required'],
            [['id', 'valid_chk', 'sort'], 'integer'],
            [['title'], 'string'],
            [['icon_key'], 'string', 'max' => 255],
            [['id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Manage Menu Category ID'),
            'title' => Yii::t('app', 'Title'),
            'valid_chk' => Yii::t('app', 'Valid Chk'),
            'sort' => Yii::t('app', 'Sort'),
            'icon_key' => Yii::t('app', 'Icon Key'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(ManageMenuMain::className(), ['manage_menu_category_id' => 'id']);
    }

    /**
     * adminIdがあれば表示可能な Menu を取得する
     * なければ全ての Menu を取得する
     * todo クエリ数増えてしまっているので設計検討
     * @param null $adminId
     * @return $this[]
     */
    public static function getMyMenu($adminId = null)
    {
        $exceptions = [];
        $roles = [];
        /** @var DbManager $authManager */
        $authManager = Yii::$app->authManager;
        /** @var Manager $identity */
        $identity = Yii::$app->user->identity;

        // $adminIdがあればそれを元にauthを調査
        if ($adminId) {
            if ($identity->myRole == Manager::OWNER_ADMIN) {
                $roles = [Manager::OWNER_ADMIN, Manager::CORP_ADMIN, Manager::CLIENT_ADMIN];
            } elseif ($identity->myRole == Manager::CORP_ADMIN) {
                $roles = [Manager::CORP_ADMIN, Manager::CLIENT_ADMIN];
            } elseif ($identity->myRole == Manager::CLIENT_ADMIN) {
                $roles = [Manager::CLIENT_ADMIN];
            }

            $exceptions = (new Query)->select('a.item_name')
                ->from(['a' => $authManager->assignmentTable, 'b' => $authManager->itemTable])
                ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
                ->andWhere(['a.user_id' => (string)$adminId])
                ->andWhere(['b.type' => Item::TYPE_PERMISSION])
                ->column($authManager->db);
        }
        return self::find()->innerJoinWith([
            'items' => function (ActiveQuery $q) use ($roles, $exceptions) {
                $q->where([
                    MenuMain::tableName() . '.valid_chk' => self::VALID_FLAG,
                    //roleによるフィルター
                ])->andFilterWhere([
                    MenuMain::tableName() . '.permitted_role' => $roles,
                    // exceptionの除外
                ])->andFilterWhere([
                    'not in',
                    MenuMain::tableName() . '.exception',
                    $exceptions,
                    // 小項目の並び替え
                ])->orderBy([
                    'sort' => SORT_ASC,
                ]);
            },
        ])->where([
            self::tableName() . '.valid_chk' => self::VALID_FLAG,
            // カテゴリーの並び替え
        ])->orderBy([
            'sort' => SORT_ASC,
        ])->all();
    }

    /**
     * そのメニューカテゴリがアクティブかどうかを判別する
     * @return bool
     */
    public function isActive()
    {
        $route = Yii::$app->requestedRoute;
        // 最後の文字が'/'なら'/'を消す
        $route = substr($route, -1) == '/' ? substr($route, 0, -1) : $route;
        // 最初の文字が'/'でなければ'/'を足す
        $route = substr($route, 0, 1) != '/' ? '/' . $route : $route;
        foreach ($this->items as $item) {
            if ($item->href == $route) {
                return true;
            }
        }
        return false;
    }

    /**
     * todo クエリ数増えてしまっているので設計検討
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getExceptionList()
    {
        return self::find()->innerJoinWith([
            'items' => function (ActiveQuery $q) {
                $q->where(['and',
                    [MenuMain::tableName() . '.valid_chk' => self::VALID_FLAG],
                    ['not', [MenuMain::tableName() . '.exception' => '',]],
                ])->orderBy([
                    'sort' => SORT_ASC,
                ]);
            },
        ])->where([
            self::tableName() . '.valid_chk' => self::VALID_FLAG,
            // カテゴリーの並び替え
        ])->orderBy([
            'sort' => SORT_ASC,
        ])->all();
    }

    /**
     * メニュー名配列を返す
     *
     * @return array
     */
    public function getNameMap(): array
    {
        return $this->nameMap;
    }

    /**
     * iconキーからメニュー名を返す
     *
     * @return string
     */
    public function getName(): string
    {
        return ArrayHelper::getValue($this->nameMap, $this->icon_key, '');
    }
}
