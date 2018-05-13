<?php

namespace app\models\manage;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\manage\SendMailSet;

/**
 * SendMailSetSearch represents the model behind the search form about `app\models\manage\SendMailSet`.
 */
class SendMailSetSearch extends SendMailSet
{
    /**
     * Constant DEFAULT_SELECT_VALUE
     * attribute mail_type is Member
     * @var int (all)
     */
    const DEFAULT_SELECT_VALUE = 'all';
    /**
     * search item
     * @var string
     */
    public $searchItem;

    /**
     * 検索テキスト
     * @var string
     */
    public $searchText;

    /**
     * itemSearchs
     * @return array
     */
    public function itemSearchs()
    {
        return [
            'from_name' => Yii::t('app', '差出人名'),
            'from_address' => Yii::t('app', '差出人メールアドレス'),
            'subject' => Yii::t('app', '件名'),
            'contents' => Yii::t('app', 'メール文面'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        //検索及び一覧で表示するラベルを設定
        return ArrayHelper::merge(parent::attributes(), [
            'searchItem',
            'searchText',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['searchItem', 'searchText',], 'safe'],
            ['mail_to', 'integer'],
            ['mail_name', 'string'],
        ];
    }

    /**
     * ラベル設定
     */
    public function attributeLabels()
    {
        //検索及び一覧で表示するラベルを設定
        return ArrayHelper::merge(parent::attributeLabels(), [
            'searchItem' => 'Item Search',
            'searchText' => 'Text Search',
        ]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SendMailSet::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->searchItem == self::DEFAULT_SELECT_VALUE) {
            $orWhere = ['or'];
            foreach (array_keys($this->itemSearchs()) as $attribute) {
                $orWhere[] = ['like', self::getColumnName($attribute, self::tableName()), $this->searchText];
            }
            $query->andFilterWhere($orWhere);
        } elseif (isset($this->searchItem)) {
            $query->andFilterWhere(['like', self::getColumnName($this->searchItem, self::tableName()), $this->searchText]);
        }


        if (isset($this->mail_to)) {
            $query->andFilterWhere(['=', self::getColumnName('mail_to', self::tableName()), $this->mail_to]);
        }
        if (isset($this->mail_name)) {
            $query->andFilterWhere(['=', self::getColumnName('mail_name', self::tableName()), $this->mail_name]);
        }


        return $dataProvider;
    }

    /**
     * カラム名を取得します。
     * リレーション関係のものは対応するリレーション先のカラム名から取得します。
     * @param string $attr attribute
     * @param string $tableName テーブル名
     * @return string value値
     */
    public static function getColumnName($attr, $tableName = null)
    {
        return isset($tableName) ? $tableName . '.' . $attr : $attr;
    }

    /**
     * constant DEFAULT_SELECT_VALUE
     * @return array (キーワード検索対象)
     */
    public function getDefaultSelectLabel()
    {
        return [
            self::DEFAULT_SELECT_VALUE => Yii::t('app', 'すべて'),
        ];
    }
}
