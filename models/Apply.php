<?php

namespace app\models;

use app\models\manage\ApplicationMasterBackup;
use app\models\manage\ApplicationStatus;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\manage\ApplicationMaster;
use yii\helpers\Html;

/**
 * 求職者画面用、応募モデル
 * 基本的には管理者側であるApplicationMasterを継承しています。
 */
class Apply extends ApplicationMaster
{
    /**
     * ユーザー登録側のシナリオ
     */
    const SCENARIO_USER_REGISTER = 'user_register';

    /**
     * DetailView用のフィールド
     * todo ここにあるのおかしいのでcolumnsetへ移動
     */
    const DETAIL_VIEW_FIELDS = [
        'birth_date' => 'birth_date:dateFormatter',
        'pref_id' => 'prefName',
        'occupation_id' => 'occupationName',
        'sex' => 'sex:sex',
    ];

    const DEFAULT_APPLICATION_STATUS_NO = 0;

    public $postalCode;

    /**
     * 求職者画面のルールは管理画面とは分離して定義しています。
     * @inheritdoc
     */
    public function rules()
    {
        $rules = ArrayHelper::merge(Yii::$app->functionItemSet->application->rules, [
            [['name_sei', 'name_mei', 'mail_address'], 'required'],
            // clientサイドでvalidationしたらEFO的に良くないかもしれないので一旦コメントアウト
//            [['name_sei', 'name_mei', 'kana_sei', 'kana_mei'], 'match', 'pattern' => '/^[\S]*$/i', 'message' => Yii::t('app', '{attribute}にスペースは使えません')],
            [['birth_date'], 'safe', 'on' => [self::SCENARIO_USER_REGISTER]],
            [['job_master_id', 'sex'], 'integer'],
            [['birthDate', 'jobMaster', 'clientMaster', 'birthDateYear', 'birthDateMonth', 'birthDateDay', 'name_sei', 'name_mei', 'kana_sei', 'kana_mei'], 'safe'],
            ['created_at', 'number'],
            ['application_status_id', 'default', 'value' => function ($model, $attribute) {
                return ApplicationStatus::find()->select('min(id)')->scalar();
            }],
            ['application_no', 'default', 'value' => function ($model, $attribute) {
                return max(self::find()->select('max(application_no)')->scalar() + 1, ApplicationMasterBackup::find()->select('max(application_no)')->scalar() + 1);
            }],
            ['postalCode', 'match', 'pattern' => '/^(\d{3}-\d{4}|\d{7})$/', 'message' => Yii::t('app', '郵便番号の書式が間違っています')],
        ]);
        //複数項目存在するフォームの場合、DBの必須を見て追加する処理
        foreach ((array)Yii::$app->functionItemSet->application->items as $items) {
            //出力予定のアイテムの中で、フリカナ・生年月日、かつ必須項目であれば子要素も一緒に必須とする。
            if (in_array($items->column_name, ['fullNameKana', 'birth_date', 'sex']) && !empty($items->is_must)) {
                switch ($items->column_name) {
                    case 'fullNameKana' :
                        $rules[] = [['kana_sei', 'kana_mei'], 'required'];
                        break;
                    case 'birth_date' :
                        $rules[] = [['birthDateYear', 'birthDateMonth', 'birthDateDay'], 'required'];
                        break;
                }
            }
        }
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $parentAttributeLabels = parent::attributeLabels();
        //バリデーションメッセージ用
        return ArrayHelper::merge($parentAttributeLabels, [
            'name_sei' => Yii::t('app', '姓'),
            'name_mei' => Yii::t('app', '名'),
            'kana_sei' => Yii::t('app', 'セイ'),
            'kana_mei' => Yii::t('app', 'メイ'),
            'prefName' => ArrayHelper::getValue($parentAttributeLabels, 'pref_id'), //detailViewFields用
            'occupationName' => ArrayHelper::getValue($parentAttributeLabels, 'occupation_id'), //detailViewFields用
            'postalCode' => Yii::t('app', '郵便番号'),
        ]);
    }

    /**
     * 保存前処理
     * @param boolean $insert インサートかどうか
     * @return boolean
     */
    public function beforeSave($insert)
    {

        if (parent::beforeSave($insert)) {
            if ($insert) {
                foreach ((array)$this->attributes as $attributeName => $attribute) {
                    //オプション項目かつ、配列（チェックボックス）の入力であったとき
                    if (strpos($attributeName, 'option') !== false && is_array($attribute)) {
                        //複数項目をカンマ区切りで1文字列にまとめる。
                        $this->setAttribute($attributeName, implode(',', $attribute));
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * TableFieldクラスtextメソッド用
     * @inheritdoc
     */
    public function fields()
    {
        /* @var $model ApplicationMaster */
        return ArrayHelper::merge(parent::fields(), [
            'birth_date' => function ($model) {
                return Yii::$app->formatter->format($model->birth_date, 'date');
            },
            'pref_id' => 'prefName',
            'occupation_id' => 'occupationName',
            'postalCode' => 'postalCode',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getFormatTable()
    {
        //性別はフォーマットテーブルとして定義
        return ArrayHelper::merge(parent::getFormatTable(), [
            'sex' => Yii::$app->formatter->sex,
        ]);
    }

    /**
     * ロード処理
     * @param array $data ロードするデータ
     * @param string $formName フォーム名
     * @return boolean
     */
    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        $this->birth_date = checkdate((int)$this->birthDateMonth, (int)$this->birthDateDay, (int)$this->birthDateYear) ? $this->birthDate : null;
        return $result;
    }

    /**
     * 氏名フルのセット
     * @param string $value 氏名
     */
    public function setFullName($value)
    {
        $name = explode(' ', $value);
        $this->name_sei = $name[0];
        $this->name_mei = $name[1];
        $this->fullName = $value;
    }

    /**
     * 氏名（フリカナ）フルのセット
     * @param string $value カナ氏名
     */
    public function setFullNameKana($value)
    {
        $name = explode(' ', $value);
        $this->kana_sei = $name[0];
        $this->kana_mei = $name[1];
        $this->fullNameKana = $value;
    }

    /**
     * occupationから属性取得
     *
     * @return string
     */
    public function getOccupationName()
    {
        if (is_object($this->occupation)) {
            return $this->occupation->occupation_name;
        }
        return '';
    }

    /**
     * ユーザーエージェントによって応募機器タイプを振り分けるセッター
     *      - 「Mobile」が含まれる場合、基本スマートフォンとして振り分ける
     *      - ただし「iPad」が含まれる場合、PCとして振り分ける
     * @param string $userAgent ユーザーエージェント
     */
    public function setCarrierTypeByUserAgent($userAgent)
    {
        if (strpos($userAgent, 'Mobile') !== false) {
            //iPadが含まれればPC、それ以外はスマホ
            $this->carrier_type = strpos($userAgent, 'iPad') !== false ? self::PC_CARRIER : self::SMART_PHONE_CARRIER;
        } else {
            //PC
            $this->carrier_type = self::PC_CARRIER;
        }
    }

    /**
     * @param $attribute
     * @return string
     */
    public function subsetString($attribute)
    {
        $subsetItemNames = [];
        $subsetInputs = [];
        foreach ((array)$this->$attribute as $i => $subsetItemName) {
            $subsetItemNames[] = Html::encode($subsetItemName);
            $subsetInputs[] = Html::activeHiddenInput($this, $attribute . '[]', ['value' => $subsetItemName]);
        }
        return implode(', ', $subsetItemNames) . implode('', $subsetInputs);
    }
}
