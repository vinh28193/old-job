<?php

namespace app\models\manage;

use Yii;
use proseeds\models\BaseModel;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%policy}}".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property int $policy_no
 * @property string $policy_name
 * @property string $description
 * @property integer $page_type
 * @property integer $from_type
 * @property string $policy
 * @property integer $valid_chk
 *
 * @property array $validChkLabel
 * @property string $url
 */
class Policy extends BaseModel
{
    /** ページ種類 求職者画面or管理画面 */
    const PAGE_TYPE_MEMBER = 0;
    const PAGE_TYPE_ADMIN = 1;

    /** 送信者 求職者or管理者 */
    const FROM_TYPE_MEMBER = 0;
    const FROM_TYPE_ADMIN = 1;

    /**
     * 公開状況
     * attribute valid_chk constant
     */
    const INVALID = 0;
    const VALID = 1;

    /** 規約種別 */
    const ADMIN_POLICY_NO = 9;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'policy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['policy_name', 'policy', 'valid_chk'], 'required'],
            [['policy_no', 'page_type', 'from_type', 'tenant_id'], 'integer'],
            [['valid_chk'], 'boolean'],
            [['policy_name'], 'string', 'max' => 30],
            [['description'], 'string', 'max' => 50],
            ['policy', 'string']
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
            'policy_no' => Yii::t('app', '規約番号'),
            'policy_name' => Yii::t('app', '規約名'),
            'description' => Yii::t('app', '規約表示箇所'),
            'page_type' => Yii::t('app', 'ページ'),
            'from_type' => Yii::t('app', 'カテゴリ'),
            'policy' => Yii::t('app', '規約'),
            'valid_chk' => Yii::t('app', '公開状況'),
            'url' => Yii::t('app', 'URL'),
        ];
    }

    /**
     * @return array
     */
    public function getValidChkLabel()
    {
        return [
            self::INVALID => Yii::t('app', '非公開'),
            self::VALID => Yii::t('app', '公開'),
        ];
    }

    /**
     * @return array
     */
    public function getFormatTable()
    {
        return [
            'page_type' => [
                self::PAGE_TYPE_MEMBER => Yii::t('app', 'ユーザー画面'),
                self::PAGE_TYPE_ADMIN => Yii::t('app', '管理画面'),
            ],
            'from_type' => [
                self::FROM_TYPE_MEMBER => Yii::t('app', 'フォーム'),
                self::FROM_TYPE_ADMIN => Yii::t('app', '個別'),
            ],
            'valid_chk' => $this->validChkLabel,
        ];
    }

    public function getUrl()
    {
        return Url::toRoute(['/policy/index/', 'policy_no' => $this->policy_no], true);
    }
}
