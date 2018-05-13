<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * Class ApplicationAuth
 * @package app\modules\manage\models
 */
class ApplyAuth extends Model
{
    /** @var int 応募ID */
    public $applicationId;

    /** @var string 氏名 */
    public $fullName;

    /** @var string 氏名 姓 */
    public $nameSei;

    /** @var string 氏名 名 */
    public $nameMei;

    /** @var string メールアドレス */
    public $mailAddress;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [
                ['applicationId', 'fullName', 'nameSei', 'nameMei', 'mailAddress'],
                'required',
                'message' => Yii::t('app', '{attribute} は必須です.'),
            ],
            ['mailAddress', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'applicationId' => Yii::t('app', '応募ID'),
            'fullName' => Yii::t('app', '氏名'),
            'nameSei' => Yii::t('app', '姓'),
            'nameMei' => Yii::t('app', '名'),
            'mailAddress' => Yii::t('app', 'メールアドレス')
        ];
    }
}