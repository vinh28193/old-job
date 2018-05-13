<?php

namespace app\models\manage;

use app\models\queries\WidgetQuery;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use proseeds\models\BaseModel;

/**
 * This is the model class for table "widget".
 *
 * @property integer $id
 * @property integer $tenant_id
 * @property integer $widget_no
 * @property string $widget_name
 * @property integer $element1
 * @property integer $element2
 * @property integer $element3
 * @property integer $widget_layout_id
 * @property integer $sort
 * @property boolean $is_disp_widget_name
 * @property integer $style_pc
 * @property integer $style_sp
 * @property integer $data_per_line_pc
 * @property integer $data_per_line_sp
 * @property integer $is_slider
 *
 * @property WidgetData[] $widgetData
 * @property WidgetLayout $widgetLayout
 * @property array $elements
 * @property string $widgetTypeName
 * @property string $cssClass
 * @property integer $widgetDataPattern
 */
class Widget extends BaseModel
{
    /** @var int getter用 */
    private $_widgetDataPattern;
    /** 要素総数 */
    const NUMBER_OF_ELEMENTS = 3;
    /** 要素 - 非表示、画像、タイトル、ディスクリプション、動画 */
    const ELEMENT_HIDE = 0;
    const ELEMENT_PICT = 1;
    const ELEMENT_TITLE = 2;
    const ELEMENT_DESCRIPTION = 3;
    const ELEMENT_MOVIE = 5;
    /** 内容を入力できる要素とWidgetDataのattributeとの対応配列 */
    const INPUT_ELEMENTS = [
        'pict' => self::ELEMENT_PICT,
        'title' => self::ELEMENT_TITLE,
        'description' => self::ELEMENT_DESCRIPTION,
        'movieTag' => self::ELEMENT_MOVIE,
    ];
    /** PCでのwidget_data表示スタイル - 縦1列 */
    const STYLE_PC_1 = 1;
    /** PCでのwidget_data表示スタイル - 画像左,文字右の2列 */
    const STYLE_PC_2 = 2;
    /** SPでのwidget_data表示スタイル - 縦1列 */
    const STYLE_SP_1 = 1;
    /** SPでのwidget_data表示スタイル - 画像左,文字右の2列 */
    const STYLE_SP_2 = 2;

    /** widget_data表示パターン定義 */
    const WIDGET_DATA_PATTERN_1 = 1;
    const WIDGET_DATA_PATTERN_2 = 2;
    const WIDGET_DATA_PATTERN_3 = 3;
    const WIDGET_DATA_PATTERN_4 = 4;
    const WIDGET_DATA_PATTERN_5 = 5;
    const WIDGET_DATA_PATTERN_6 = 6;
    const WIDGET_DATA_PATTERN_7 = 7;
    const WIDGET_DATA_PATTERNS = [
        self::WIDGET_DATA_PATTERN_1 => [
            'element1' => self::ELEMENT_PICT,
            'element2' => self::ELEMENT_TITLE,
            'element3' => self::ELEMENT_DESCRIPTION,
            'style_pc' => self::STYLE_PC_1,
            'is_slider' => self::IS_NOT_SLIDER,
        ],
        self::WIDGET_DATA_PATTERN_2 => [
            'element1' => self::ELEMENT_PICT,
            'element2' => self::ELEMENT_TITLE,
            'element3' => self::ELEMENT_DESCRIPTION,
            'style_pc' => self::STYLE_PC_2,
            'is_slider' => self::IS_NOT_SLIDER,
        ],
        self::WIDGET_DATA_PATTERN_3 => [
            'element1' => self::ELEMENT_TITLE,
            'element2' => self::ELEMENT_DESCRIPTION,
            'element3' => self::ELEMENT_HIDE,
            'style_pc' => self::STYLE_PC_1,
            'is_slider' => self::IS_NOT_SLIDER,
        ],
        self::WIDGET_DATA_PATTERN_4 => [
            'element1' => self::ELEMENT_PICT,
            'element2' => self::ELEMENT_TITLE,
            'element3' => self::ELEMENT_HIDE,
            'style_pc' => self::STYLE_PC_1,
            'is_slider' => self::IS_NOT_SLIDER,
        ],
        self::WIDGET_DATA_PATTERN_5 => [
            'element1' => self::ELEMENT_PICT,
            'element2' => self::ELEMENT_HIDE,
            'element3' => self::ELEMENT_HIDE,
            'style_pc' => self::STYLE_PC_1,
            'is_slider' => self::IS_NOT_SLIDER,
        ],
        self::WIDGET_DATA_PATTERN_6 => [
            'element1' => self::ELEMENT_MOVIE,
            'element2' => self::ELEMENT_HIDE,
            'element3' => self::ELEMENT_HIDE,
            'style_pc' => self::STYLE_PC_1,
            'is_slider' => self::IS_NOT_SLIDER,
        ],
        self::WIDGET_DATA_PATTERN_7 => [
            'element1' => self::ELEMENT_PICT,
            'element2' => self::ELEMENT_HIDE,
            'element3' => self::ELEMENT_HIDE,
            'style_pc' => self::STYLE_PC_1,
            'is_slider' => self::IS_SLIDER,
        ],
    ];
    /** data_per_line表示定義 */
    const ONE_DATA_PER_LINE = 1;
    const TWO_DATA_PER_LINE = 2;
    const THREE_DATA_PER_LINE = 3;
    const FOUR_DATA_PER_LINE = 4;
    /** is_display_widget_name表示定義 */
    const IS_NOT_DISP_WIDGET_NAME_LABEL = 0;
    const IS_DISP_WIDGET_NAME_LABEL = 1;
    /** スライダー機能のON/OFF */
    const IS_NOT_SLIDER = 0;
    const IS_SLIDER = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'widget';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'tenant_id',
                    'widget_no',
                    'widget_name',
                    'widgetDataPattern',
                    'style_sp',
                    'data_per_line_pc',
                    'data_per_line_sp',
                    'is_disp_widget_name',
                    'is_slider',
                ],
                'required',
            ],
            [
                [
                    'tenant_id',
                    'widget_no',
                    'element1',
                    'element2',
                    'element3',
                    'widget_layout_id',
                    'sort',
                    'style_pc',
                    'style_sp',
                    'data_per_line_pc',
                    'data_per_line_sp',
                    'is_disp_widget_name',
                ],
                'integer',
            ],
            ['widgetDataPattern', 'safe'],
            [['widget_name'], 'string', 'max' => 255],
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
            'widget_no' => Yii::t('app', 'ウィジェットナンバー'),
            'widget_name' => Yii::t('app', 'ウィジェット名'),
            'element1' => Yii::t('app', 'コンテンツ内で1番目に表示させる要素'),
            'element2' => Yii::t('app', 'コンテンツ内で2番目に表示させる要素'),
            'element3' => Yii::t('app', 'コンテンツ内で3番目に表示させる要素'),
            'widget_layout_id' => Yii::t('app', 'ウィジェットNo(1～10)'),
            'sort' => Yii::t('app', 'ウィジェット表示順'),
            'is_disp_widget_name' => Yii::t('app', 'ウィジェット名の見出し表示'),
            'style_pc' => Yii::t('app', '表示スタイル（PC版）'),
            'style_sp' => Yii::t('app', '表示スタイル（SP版）'),
            'data_per_line_pc' => Yii::t('app', 'ウィジェットデータ数（PC版）'),
            'data_per_line_sp' => Yii::t('app', 'ウィジェットデータ数（SP版）'),
            'widgetDataPattern' => Yii::t('app', 'ウィジェット表示パターン'),
        ];
    }

    /**
     * 表示要素を配列で取得
     * @return array
     */
    public function getElements()
    {
        return [
            'element1' => $this->element1,
            'element2' => $this->element2,
            'element3' => $this->element3,
        ];
    }

    /**
     * widgetのcss classの出力を
     * 計3パターンで切り替え
     * @return string
     */
    public function getCssClass()
    {
        // 共通クラス
        $class = [
            'widget',
            "widget{$this->widget_no}",
        ];
        // スライダー機能OFFの時に追加されるクラス
        if ($this->is_slider === Widget::IS_NOT_SLIDER) {
            $class = array_merge($class, [
                "box-pc-{$this->data_per_line_pc}",
                "box-sp-{$this->data_per_line_sp}",
                "style-pc-{$this->style_pc}",
                "style-sp-{$this->style_sp}",
            ]);
        }
        // 動画の時にさらに追加されるクラス
        if (in_array(self::ELEMENT_MOVIE, $this->elements)) {
            $class[] = 'style-movie';
        }
        return implode(' ', $class);
    }

    /**
     * @param $value
     * @return mixed
     * @throws Exception
     */
    public function setWidgetDataPattern($value)
    {
        $this->_widgetDataPattern = $value;
        $data = ArrayHelper::getValue(self::WIDGET_DATA_PATTERNS, $this->widgetDataPattern);
        if (!$data) {
            throw new Exception();
        }
        $this->setAttributes($data);
    }

    /**
     * @return int
     */
    public function getWidgetDataPattern()
    {
        if (!$this->_widgetDataPattern) {
            $pattern = ArrayHelper::merge($this->getElements(), ['style_pc' => $this->style_pc, 'is_slider' => $this->is_slider]);
            $result = array_search($pattern, self::WIDGET_DATA_PATTERNS);
            $this->_widgetDataPattern = ($result === false) ? null : $result;
        }
        return $this->_widgetDataPattern;
    }

    /**
     * ドロップダウンで使う配列を取得する
     * @return array
     */
    public static function getDropDownArray()
    {
        return ArrayHelper::map(self::find()->select(['id', 'widget_name'])->all(), 'id', 'widget_name');
    }

    /**
     * ウィジェットデータリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getWidgetData()
    {
        return $this->hasMany(WidgetData::className(), ['widget_id' => 'id'])->inverseOf('widget');
    }

    /**
     * widgetLayoutリレーション
     * @return \yii\db\ActiveQuery
     */
    public function getWidgetLayout()
    {
        return $this->hasOne(WidgetLayout::className(), ['id' => 'widget_layout_id']);
    }

    /**
     * @return mixed
     */
    public function setStyleSp()
    {
        if ($this->widgetDataPattern != self::WIDGET_DATA_PATTERN_1 && $this->widgetDataPattern != self::WIDGET_DATA_PATTERN_2) {
            $this->style_sp = self::STYLE_SP_1;
        }
    }

    /**
     * is_disp_widget_nameのラベル配列を取得する
     * @return array
     */
    public static function getIsDispWidgetNameLabels()
    {
        return [
            self::IS_NOT_DISP_WIDGET_NAME_LABEL => Yii::t('app', '非表示'),
            self::IS_DISP_WIDGET_NAME_LABEL => Yii::t('app', '表示'),
        ];
    }

    /**
     * WidgetDataPatternのラベル配列を取得する
     * @return array
     */
    public static function getWidgetDataPatternLabels()
    {
        return [
            self::WIDGET_DATA_PATTERN_1 => Yii::t('app', 'パターン 1'),
            self::WIDGET_DATA_PATTERN_2 => Yii::t('app', 'パターン 2'),
            self::WIDGET_DATA_PATTERN_3 => Yii::t('app', 'パターン 3'),
            self::WIDGET_DATA_PATTERN_4 => Yii::t('app', 'パターン 4'),
            self::WIDGET_DATA_PATTERN_5 => Yii::t('app', 'パターン 5'),
            self::WIDGET_DATA_PATTERN_6 => Yii::t('app', 'パターン 6'),
            self::WIDGET_DATA_PATTERN_7 => Yii::t('app', 'パターン 7'),
        ];
    }

    /**
     * style_spのラベル配列を取得する
     * @return array
     */
    public static function getStyleSpLabels()
    {
        return [
            self::STYLE_SP_1 => Yii::t('app', '画像を上に表示'),
            self::STYLE_SP_2 => Yii::t('app', '画像を左に表示'),
        ];
    }

    /**
     * data_per_line_pcのラベル配列を取得する
     * @return array
     */
    public static function getDataPerLinePcLabels()
    {
        return [
            self::ONE_DATA_PER_LINE => Yii::t('app', '横に1つ'),
            self::TWO_DATA_PER_LINE => Yii::t('app', '横に2つ'),
            self::THREE_DATA_PER_LINE => Yii::t('app', '横に3つ'),
            self::FOUR_DATA_PER_LINE => Yii::t('app', '横に4つ'),
        ];
    }

    /**
     * data_per_line_spのラベル配列を取得する
     * @return array
     */
    public static function getDataPerLineSpLabels()
    {
        return [
            self::ONE_DATA_PER_LINE => Yii::t('app', '横に1つ'),
            self::TWO_DATA_PER_LINE => Yii::t('app', '横に2つ'),
        ];
    }

    /**
     * @return WidgetQuery
     */
    public static function find()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(WidgetQuery::className(), [get_called_class()]);
    }
}
