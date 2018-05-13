<?php
namespace models\manage;

use app\models\queries\WidgetQuery;
use tests\codeception\unit\JmTestCase;
use app\models\manage\Widget;
use tests\codeception\unit\fixtures\WidgetFixture;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @group widgets
 * @property WidgetFixture $widget
 */
class WidgetTest extends JmTestCase
{
    /**
     * テーブル名テスト
     */
    public function testTableName()
    {
        $model = new Widget();
        verify($model->tableName())->equals('widget');
    }

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('必要チェック', function () {
            $model = new Widget();
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('widget_no'))->true();
            verify($model->hasErrors('widget_name'))->true();
            verify($model->hasErrors('widgetDataPattern'))->true();
            verify($model->hasErrors('style_sp'))->true();
            verify($model->hasErrors('data_per_line_pc'))->true();
            verify($model->hasErrors('data_per_line_sp'))->true();
            verify($model->hasErrors('is_disp_widget_name'))->true();
            verify($model->hasErrors('is_slider'))->true();
        });
        $this->specify('数字チェック', function () {
            $model = new Widget();
            $model->load([
                $model->formName() => [
                    'tenant_id' => '文字列',
                    'widget_no' => '文字列',
                    'element1' => '文字列',
                    'element2' => '文字列',
                    'element3' => '文字列',
                    'widget_layout_id' => '文字列',
                    'sort' => '文字列',
                    'style_pc' => '文字列',
                    'style_sp' => '文字列',
                    'data_per_line_pc' => '文字列',
                    'data_per_line_sp' => '文字列',
                    'is_disp_widget_name' => '文字列',
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('tenant_id'))->true();
            verify($model->hasErrors('widget_no'))->true();
            verify($model->hasErrors('element1'))->true();
            verify($model->hasErrors('element2'))->true();
            verify($model->hasErrors('element3'))->true();
            verify($model->hasErrors('widget_layout_id'))->true();
            verify($model->hasErrors('sort'))->true();
            verify($model->hasErrors('style_pc'))->true();
            verify($model->hasErrors('style_sp'))->true();
            verify($model->hasErrors('data_per_line_pc'))->true();
            verify($model->hasErrors('data_per_line_sp'))->true();
            verify($model->hasErrors('is_disp_widget_name'))->true();
        });

        $this->specify('文字列チェック', function () {
            $model = new Widget();
            $model->load([
                $model->formName() => [
                    'widget_name' => 1,
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('widget_name'))->true();
        });

        $this->specify('文字列最大値チェック', function () {
            $model = new Widget();
            $model->load([
                $model->formName() => [
                    'widget_name' => str_repeat('a', 256),
                ],
            ]);
            $model->validate();
            verify($model->hasErrors('widget_name'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new Widget();
            $model->load([
                $model->formName() => [
                    'tenant_id' => 1,
                    'widget_no' => 1,
                    'element1' => 1,
                    'element2' => 1,
                    'element3' => 1,
                    'widget_layout_id' => 1,
                    'sort' => 1,
                    'style_pc' => 1,
                    'style_sp' => 1,
                    'data_per_line_pc' => 1,
                    'data_per_line_sp' => 1,
                    'is_disp_widget_name' => 1,
                    'widget_name' => '',
                    'is_slider' => 1,
                ],
            ]);
            verify($model->validate())->false();
        });
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new Widget();
        verify(count($model->attributeLabels()))->notEmpty();
    }

    /**
     * getElementsのtest
     */
    public function testGetElements()
    {
        $model = new Widget();
        $model->element1 = 4;
        $model->element2 = 2;
        $model->element3 = 1;
        verify(ArrayHelper::getValue($model->elements, 'element1'))->equals(4);
        verify(ArrayHelper::getValue($model->elements, 'element2'))->equals(2);
        verify(ArrayHelper::getValue($model->elements, 'element3'))->equals(1);
    }

    /**
     * getDropDownArrayのtest
     */
    public function testGetDropDownArray()
    {
        verify(Widget::getDropDownArray())->count(10);
        $array = Widget::getDropDownArray();
        foreach ($array as $id => $value) {
            $record = $this->findRecordById(self::getFixtureInstance('widget'), $id);
            verify($record['widget_name'])->equals($value);
        }
    }

    /**
     * getAndSetWidgetDataPatternのtest
     */
    public function testGetAndSetWidgetDataPattern()
    {
        $this->specify('widgetDataPatternをsetしたら各種attributeに適切な値が入る', function () {
            $widgetDataPattern = 1;
            $model = new Widget();
            $model->widgetDataPattern = $widgetDataPattern;
            verify($model->widgetDataPattern)->equals($widgetDataPattern);
            verify($model->element1)->equals(Widget::WIDGET_DATA_PATTERNS[$widgetDataPattern]['element1']);
            verify($model->element2)->equals(Widget::WIDGET_DATA_PATTERNS[$widgetDataPattern]['element2']);
            verify($model->element3)->equals(Widget::WIDGET_DATA_PATTERNS[$widgetDataPattern]['element3']);
            verify($model->style_pc)->equals(Widget::WIDGET_DATA_PATTERNS[$widgetDataPattern]['style_pc']);
            verify($model->is_slider)->equals(Widget::WIDGET_DATA_PATTERNS[$widgetDataPattern]['is_slider']);
        });
        //testGetWidgetDataPattern
        $this->specify('各種attributeに対応する値が入っていたらwidgetDataPatternが取得できる', function () {
            foreach (Widget::WIDGET_DATA_PATTERNS as $name => $attribute) {
                $model = new Widget($attribute);
                verify($model->widgetDataPattern)->equals($name);
            }
        });
    }

    /**
     * setStyleSpのtest
     */
    public function testSetStyleSp()
    {
        // style_spの上書きが無いパターン
        $notOverwritePatterns = [
            Widget::WIDGET_DATA_PATTERN_1,
            Widget::WIDGET_DATA_PATTERN_2,
        ];

        foreach ($notOverwritePatterns as $pattern) {
            $styleSp = 999;
            $model = new Widget();
            $model->widgetDataPattern = $pattern;
            $model->style_sp = $styleSp;
            $model->setStyleSp();
            verify($model->style_sp)->equals($styleSp);
        }

        // style_spの上書きがあるパターン
        $overwritePatterns = [
            Widget::WIDGET_DATA_PATTERN_3,
            Widget::WIDGET_DATA_PATTERN_4,
            Widget::WIDGET_DATA_PATTERN_5,
            Widget::WIDGET_DATA_PATTERN_6,
        ];

        foreach ($overwritePatterns as $pattern) {
            $styleSp = 999;
            $model = new Widget();
            $model->widgetDataPattern = $pattern;
            $model->style_sp = $styleSp;
            $model->setStyleSp();
            verify($model->style_sp)->equals(Widget::STYLE_SP_1);
        }
    }

    /**
     * testGetIsDispWidgetNameLabelsのtest
     */
    public function testGetIsDispWidgetNameLabels()
    {
        verify(Widget::getIsDispWidgetNameLabels()[Widget::IS_NOT_DISP_WIDGET_NAME_LABEL])->equals(Yii::t('app', '非表示'));
        verify(Widget::getIsDispWidgetNameLabels()[Widget::IS_DISP_WIDGET_NAME_LABEL])->equals(Yii::t('app', '表示'));
    }

    /**
     * testGetWidgetDataPatternLabelsのtest
     */
    public function testGetWidgetDataPatternLabels()
    {
        verify(Widget::getWidgetDataPatternLabels()[Widget::WIDGET_DATA_PATTERN_1])->equals(Yii::t('app', 'パターン 1'));
        verify(Widget::getWidgetDataPatternLabels()[Widget::WIDGET_DATA_PATTERN_2])->equals(Yii::t('app', 'パターン 2'));
        verify(Widget::getWidgetDataPatternLabels()[Widget::WIDGET_DATA_PATTERN_3])->equals(Yii::t('app', 'パターン 3'));
        verify(Widget::getWidgetDataPatternLabels()[Widget::WIDGET_DATA_PATTERN_4])->equals(Yii::t('app', 'パターン 4'));
        verify(Widget::getWidgetDataPatternLabels()[Widget::WIDGET_DATA_PATTERN_5])->equals(Yii::t('app', 'パターン 5'));
        verify(Widget::getWidgetDataPatternLabels()[Widget::WIDGET_DATA_PATTERN_6])->equals(Yii::t('app', 'パターン 6'));
        verify(Widget::getWidgetDataPatternLabels()[Widget::WIDGET_DATA_PATTERN_7])->equals(Yii::t('app', 'パターン 7'));
    }

    /**
     * testGetStyleSpLabelsのtest
     */
    public function testGetStyleSpLabels()
    {
        verify(Widget::getStyleSpLabels()[Widget::STYLE_SP_1])->equals(Yii::t('app', '画像を上に表示'));
        verify(Widget::getStyleSpLabels()[Widget::STYLE_SP_2])->equals(Yii::t('app', '画像を左に表示'));
    }

    /**
     * testGetDataPerLinePcLabelsのtest
     */
    public static function testGetDataPerLinePcLabels()
    {
        verify(Widget::getDataPerLinePcLabels()[Widget::ONE_DATA_PER_LINE])->equals(Yii::t('app', '横に1つ'));
        verify(Widget::getDataPerLinePcLabels()[Widget::TWO_DATA_PER_LINE])->equals(Yii::t('app', '横に2つ'));
        verify(Widget::getDataPerLinePcLabels()[Widget::THREE_DATA_PER_LINE])->equals(Yii::t('app', '横に3つ'));
        verify(Widget::getDataPerLinePcLabels()[Widget::FOUR_DATA_PER_LINE])->equals(Yii::t('app', '横に4つ'));
    }

    /**
     * testGetDataPerLineSPLabelsのtest
     */
    public static function testGetDataPerLineSpLabels()
    {
        verify(Widget::getDataPerLineSpLabels()[Widget::ONE_DATA_PER_LINE])->equals(Yii::t('app', '横に1つ'));
        verify(Widget::getDataPerLineSpLabels()[Widget::TWO_DATA_PER_LINE])->equals(Yii::t('app', '横に2つ'));
    }

    /**
     * findの
     */
    public function testFind()
    {
        verify(Widget::find())->isInstanceOf(WidgetQuery::className());
    }
}
