<?php

namespace app\models;

use Yii;
use app\models\manage\AdminMaster;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "admin_master".
 * @property PasswordReminder $passwordReminder
 */
class AdminPasswordSetting extends AdminMaster
{
    //--------------------------
    // メンバ変数
    //--------------------------
    /** @var string パスワード再入力用フィールド */
    public $passwordRepeat;

    /**
     * ルール設定。
     * @return array ルール設定
     */
    public function rules()
    {
        //管理者の検証ルールに加えて、追加設定
        return ArrayHelper::merge(
            parent::rules(),
            [
                [['passwordRepeat'], 'match', 'pattern' => '/^[a-z0-9]*$/i', 'message' => Yii::t('app', '{attribute}は半角英数字にしてください。')],
                [['passwordRepeat'], 'string', 'min' => self::MIN_LOGIN_ID],
                [['passwordRepeat'], 'required'],
                ['passwordRepeat', 'compare', 'compareAttribute' => 'password'],
            ]
        );
    }

    /**
     * 要素のラベル名を設定。
     * function_item_setテーブルから名前を取得し、カラムごとに適用しています。
     * @return array ラベル設定
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'passwordRepeat' => Yii::t('app', 'パスワード（再入力）'),
            ]
        );
    }

    /**
     * 項目リレーション
     * @return \yii\db\ActiveQuery
     */
    public function getPasswordReminder()
    {
        return $this->hasOne(PasswordReminder::className(), ['key_id' => 'id']);
    }
}
