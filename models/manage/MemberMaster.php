<?php

namespace app\models\manage;

use proseeds\models\BaseModel;
use Yii;

/**
 * This is the model class for table "member_master".
 *
 * @property integer $member_id
 * @property string $password
 * @property string $name_sei
 * @property string $name_mei
 * @property string $kana_sei
 * @property string $kana_mei
 * @property string $mail_address
 * @property string $sex_type
 * @property string $login_id
 * @property string $birthdate
 * @property integer $occupation_id
 * @property integer $area_id
 * @property integer $mail_address_flg
 * @property string $option100
 * @property string $option101
 * @property string $option102
 * @property string $option103
 * @property string $option104
 * @property string $option105
 * @property string $option106
 * @property string $option107
 * @property string $option108
 * @property string $option109
 * @property string $created_at
 * @property integer $carrier_type
 * @property string $skill
 * @property integer $pref_id
 * @property string $address
 * @property string $tel_no
 * @property integer $todayCount
 * @property integer $totalCount
 */
class MemberMaster extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'password', 'name_sei', 'name_mei', 'kana_sei', 'kana_mei', 'mail_address', 'sex_type', 'skill'], 'required'],
            [['member_id', 'occupation_id', 'area_id', 'mail_address_flg', 'carrier_type', 'pref_id'], 'integer'],
            [['password', 'name_sei', 'name_mei', 'kana_sei', 'kana_mei', 'mail_address', 'sex_type', 'option100', 'option101', 'option102', 'option103', 'option104', 'option105', 'option106', 'option107', 'option108', 'option109', 'skill', 'address', 'tel_no'], 'string'],
            [['birthdate', 'created_at'], 'safe'],
            [['login_id'], 'string', 'max' => 255],
            [['member_id'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'member_id' => Yii::t('app', 'Member ID'),
            'password' => Yii::t('app', 'Password'),
            'name_sei' => Yii::t('app', 'Name Sei'),
            'name_mei' => Yii::t('app', 'Name Mei'),
            'kana_sei' => Yii::t('app', 'Kana Sei'),
            'kana_mei' => Yii::t('app', 'Kana Mei'),
            'mail_address' => Yii::t('app', 'Mail Address'),
            'sex_type' => Yii::t('app', 'Sex Type'),
            'login_id' => Yii::t('app', 'Login ID'),
            'birthdate' => Yii::t('app', 'Birthdate'),
            'occupation_id' => Yii::t('app', 'Occupation'),
            'area_id' => Yii::t('app', 'Area'),
            'mail_address_flg' => Yii::t('app', 'Mail Address Flg'),
            'option100' => Yii::t('app', 'Option100'),
            'option101' => Yii::t('app', 'Option101'),
            'option102' => Yii::t('app', 'Option102'),
            'option103' => Yii::t('app', 'Option103'),
            'option104' => Yii::t('app', 'Option104'),
            'option105' => Yii::t('app', 'Option105'),
            'option106' => Yii::t('app', 'Option106'),
            'option107' => Yii::t('app', 'Option107'),
            'option108' => Yii::t('app', 'Option108'),
            'option109' => Yii::t('app', 'Option109'),
            'created_at' => Yii::t('app', 'Regist Datetime'),
            'carrier_type' => Yii::t('app', 'Carrier Type'),
            'skill' => Yii::t('app', 'Skill'),
            'pref_id' => Yii::t('app', 'Pref'),
            'address' => Yii::t('app', 'Address'),
            'tel_no' => Yii::t('app', 'Tel No'),
        ];
    }

    public function getTodayCount()
    {
        return $this->find()->where(['between', 'created_at', date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')])->count();
    }

    public function getTotalCount()
    {
        return $this->find()->count();
    }
}
