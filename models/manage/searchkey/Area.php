<?php

namespace app\models\manage\searchkey;

use app\models\MainVisual;
use app\modules\manage\models\requests\MainVisualForm;
use proseeds\models\BaseModel;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "area".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property string $area_name
 * @property integer $valid_chk
 * @property string $area_tab_name
 * @property integer $sort
 * @property integer $area_no
 * @property string $area_dir
 * @property array $prefId
 * @property Pref[] $pref
 *
 * @property MainVisual $mainVisual
 */
class Area extends BaseModel
{
    /** 状態 - 有効or無効 */
    const FLAG_VALID = 1;
    const FLAG_INVALID = 0;

    /** 全国エリア扱いにするid */
    const NATIONWIDE_ID = 0;

    /** @var string $localPath */
    public $localPath;

    /** @var string */
    public $areaUrl;

    public $updatePrefId;

    private $_prefId;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['prefId', 'safe'],
            [['area_name', 'valid_chk', 'sort', 'area_no', 'area_dir'], 'required'],
            [['valid_chk', 'sort', 'area_no'], 'integer'],
            [['area_name', 'area_tab_name', 'area_dir'], 'string', 'max' => 50],
            [['area_dir'], 'match', 'pattern' => '/^[a-zA-Z0-9!-\/:-@¥[-`{-~]+$/i', 'message' => Yii::t('app', '{attribute}は半角文字にしてください。')],
            ['valid_chk', function ($attribute, $params) {
                if ($this->valid_chk == MainVisualForm::STATUS_CLOSED && $this->isAreaValidChk()) {
                    $this->addError($attribute, Yii::t('app', 'エリアは全て無効にできません。'));
                }
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'area_name' => Yii::t('app', 'エリア名'),
            'valid_chk' => Yii::t('app', '状態'),
            'area_tab_name' => Yii::t('app', 'エリアタブ名'),
            'sort' => Yii::t('app', '表示順'),
            'area_no' => Yii::t('app', 'エリアコード'),
            'area_dir' => Yii::t('app', 'エリアURL名'),
            'prefId' => Yii::t('app', '都道府県'),
        ];
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllOrdered()
    {
        return self::find()->orderBy(['sort' => SORT_ASC])->all();
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findAllAreaDir()
    {
        return array_column(self::find()->select('area_dir')->groupBy('area_dir')->asArray()->all(), 'area_dir');
    }

    /**
     * prefとのrelation
     * @return \yii\db\ActiveQuery
     */
    public function getPref()
    {
        return $this->hasMany(Pref::className(), ['area_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * prefのidのgetter
     * @return array
     */
    public function getPrefId()
    {
        if (!$this->_prefId) {
            $this->_prefId = ArrayHelper::getColumn($this->pref, 'id');
        }
        return $this->_prefId;
    }

    /**
     * prefのidのsetter
     * @param $value
     * @return mixed
     */
    public function setPrefId($value)
    {
        return $this->_prefId = $value;
    }

    /**
     * エリアが全て無効かどうかを調べる
     * todo 非常にパフォーマンスが悪い書き方しているので要改修
     * @return bool
     */
    protected function isAreaValidChk()
    {
        //有効なエリアを検索
        $count = self::find()->select('valid_chk')->where(['valid_chk' => 1])->count();
        //有効なエリアが最後の1つだった場合かつ、最後の1つの状態を無効にしようとした場合
        $lastAreaChk = self::find()->select('valid_chk')->where(['id' => $this->id, 'valid_chk' => 1])->count();
        if ($count == 1 && $lastAreaChk == 1) {
            return true;
        }
        return false;
    }

    /**
     * 現在サイトが1エリア化されているかの真偽を返す
     * todo componentの方を使うように変更する必要あり
     * @return bool
     */
    public static function isOneArea()
    {
        return Area::find()
            ->where(['valid_chk' => Area::FLAG_VALID])
            ->count() == 1;
    }

    /**
     * 全国エリアを取得する
     * @return Area
     */
    public static function nationwideArea()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(['class' => self::className(), 'id' => self::NATIONWIDE_ID, 'area_name' => Yii::t('app', '全国')]);
    }

    /**
     * メインビジュアルの取得
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMainVisual()
    {
        return $this->hasOne(MainVisual::className(), ['area_id' => 'id'])
            ->andWhere([MainVisual::tableName() . '.valid_chk' => 1]);
    }
}
