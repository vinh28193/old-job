<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tool_master".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $tool_id
 * @property string $page_name
 * @property string $title
 * @property string $description
 * @property string $keywords
 * @property string $h1
 */
class ToolMaster extends \proseeds\models\BaseModel
{

    /**
     * titleに設定できる最大文字数
     */
    const TITLE_MAX_LENGTH = 50;

    /**
     * descriptionに設定できる最大文字数
     */
    const DESCRIPTION_MAX_LENGTH = 200;

    /**
     * keywordsに設定できる最大文字数
     */
    const KEYWORDS_MAX_LENGTH = 200;

    /**
     * h1に設定できる最大文字数
     */
    const H1_MAX_LENGTH = 100;

    /**
     * title, description, keywords, h1が設定されてない場合のデフォルト値
     */
    const DEFAULT_VALUE = '[SITENAME]';

    /**
     * 値が設定されてない場合、デフォルト値が設定されるカラム名
     */
    const DEFAULT_COLUMNS = [
        'title',
        'description',
        'keywords',
        'h1'
    ];

    /**
     * 各ページに設定するtool_noの対応表
     */
    const TOOLNO_MAP = [
        // TOP
        'top' => 1,
        // エリアトップ
        'areatop' => 2,
        // 検索結果ページ（条件無し）
        'searchResult' => 3,
        // 検索結果ページ（検索条件が1つ）
        'searchResultOne' => 4,
        // 検索結果ページ（検索条件が2つ）
        'searchResultTwo' => 5,
        // 検索結果ページ（上記以外）
        'searchResultOther' => 6,
        // 原稿詳細ページ
        'manuscriptDetai' => 7,
        // 応募入力ページ
        'applicationInput' => 8,
        // 応募確認ページ
        'applicationConfirmation' => 9,
        // 応募完了ページ
        'applicationCompleted' => 10,
        // 携帯に送るページ
        'sendMobileInput' => 11,
        // 携帯に送る完了ページ
        'sendMobileCompleted' => 12,
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tool_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $message = Yii::t('app', 'noとページ名は変更不可です');

        return [
            [['tool_no', 'page_name', 'title', 'description', 'keywords', 'h1'], 'required'],
            [['tenant_id', 'tool_no'], 'integer'],
            [['tool_no'], 'exist', 'targetAttribute' => ['tool_no', 'page_name'], 'message' => $message],
            [['title'], 'string', 'max' => self::TITLE_MAX_LENGTH],
            [['description'], 'string', 'max' => self::DESCRIPTION_MAX_LENGTH],
            [['keywords'], 'string', 'max' => self::KEYWORDS_MAX_LENGTH],
            [['h1'], 'string', 'max' => self::H1_MAX_LENGTH],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', '主キー'),
            'tenant_id' => Yii::t('app', 'テナントID'),
            'tool_no' => Yii::t('app', 'no'),
            'page_name' => Yii::t('app', 'ページ名'),
            'title' => Yii::t('app', 'title'),
            'description' => Yii::t('app', 'description'),
            'keywords' => Yii::t('app', 'keywords'),
            'h1' => Yii::t('app', 'h1'),
        ];
    }
}
