<?php


use app\common\ProseedsFormatter;
use yii\helpers\Html;

class ProseedsFormatterTest extends \yii\codeception\DbTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     * OnOffのformatterのtest
     */
    public function testAsOnOff()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asOnOff(null))->equals('');
        verify($formatter->asOnOff(0))->equals('－');
        verify($formatter->asOnOff(1))->equals('○');
    }

    /**
     * 有効無効formatterのtest
     */
    public function testAsValidChk()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asValidChk(null))->equals('');
        verify($formatter->asValidChk(0))->equals('無効');
        verify($formatter->asValidChk(1))->equals('有効');
    }

    /**
     * 必須任意formatterのtest
     */
    public function testAsIsMustItem()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsMustItem(null))->equals('必須（固定）');
        verify($formatter->asIsMustItem(0))->equals('任意');
        verify($formatter->asIsMustItem(1))->equals('必須');
    }

    /**
     * (is_search_menu_item）非表示or表示のformatterのtest
     */
    public function testAsIsListMenuItem()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsListMenuItem(null))->equals('非表示（固定）');
        verify($formatter->asIsListMenuItem(0))->equals('非表示');
        verify($formatter->asIsListMenuItem(1))->equals('表示');
    }

    /**
     * formatterのtest
     */
    public function testAsIsSearchMenuItem()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsSearchMenuItem(null))->equals('表示（固定）');
        verify($formatter->asIsSearchMenuItem(0))->equals('非表示');
        verify($formatter->asIsSearchMenuItem(1))->equals('表示');
    }

    /**
     * 公開非公開formatterのtest
     */
    public function testAsIsPublished()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsPublished(null))->equals('');
        verify($formatter->asIsPublished(0))->equals('非公開');
        verify($formatter->asIsPublished(1))->equals('公開');
    }

    /**
     * メール送信先formatterのtest
     */
    public function testAsIsMailTo()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsMailTo(null))->equals('');
        verify($formatter->asIsMailTo(0))->equals('求職者');
        verify($formatter->asIsMailTo(1))->equals('運営元');
        verify($formatter->asIsMailTo(2))->equals('代理店');
        verify($formatter->asIsMailTo(3))->equals('掲載企業');
    }

    /**
     * キャリア種別formatterのtest
     */
    public function testAsCarrierType()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asCarrierType(null))->equals('');
        verify($formatter->asCarrierType(0))->equals('PC');
        verify($formatter->asCarrierType(1))->equals('スマートフォン');
    }

    /**
     * マップURLリンクformatterのtest
     */
    public function testAsMapUrl()
    {
        $formatter = new ProseedsFormatter();
        $value = 'test' ;
        verify($formatter->asMapUrl(null))->equals('');
        verify($formatter->asMapUrl($value))->equals('<a href="test" target="_blank">>>アクセスする</a>');
    }

    /**
     * 性別formatterのtest
     */
    public function testAsSex()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asSex(null))->equals('');
        verify($formatter->asSex(0))->equals('男性');
        verify($formatter->asSex(1))->equals('女性');
    }

    /**
     * ウィジェット種別formatterのtest
     */
    public function testAsWidgetType()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asWidgetType(null))->equals('');
        verify($formatter->asWidgetType(0))->equals('URL');
        verify($formatter->asWidgetType(1))->equals('動画');
        verify($formatter->asWidgetType(2))->equals('スライドショー');
    }

    /**
     * 新窓リンクformatterのtest
     */
    public function testAsNewWindowUrl()
    {
        $formatter = new ProseedsFormatter();
        $value = 'aaaa';
        verify($formatter->asNewWindowUrl(null))->equals('');
        verify($formatter->asNewWindowUrl($value,['class' => 'test']))->equals('<a class="test" href="http://aaaa" target="_blank">aaaa</a>');
    }

    /**
     * htmlエンコードして改行だけを反映させる
     */
    public function testAsJobView()
    {
        $formatter = new ProseedsFormatter();
        $value = '<a>改行
改行</a>';
        verify($formatter->asJobView(null))->equals('');
        verify($formatter->asJobView($value))->equals('&lt;a&gt;改行<br />
改行&lt;/a&gt;');
    }

    /**
     * 検索条件formatterのtest
     */
    public function testasIsAndSearch()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsAndSearch(null))->equals('or（固定）');
        verify($formatter->asIsAndSearch(0))->equals('or');
        verify($formatter->asIsAndSearch(1))->equals('and');
    }

    /**
     * 入力方法（PC）formatterのtest
     */
    public function testAsSearchInputTool()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asSearchInputTool(null))->equals('モーダル（固定）');
        verify($formatter->asSearchInputTool(1))->equals('モーダル');
        verify($formatter->asSearchInputTool(2))->equals('チェックボックス');
        verify($formatter->asSearchInputTool(3))->equals('プルダウン');
    }

    /**
     * 入力方法（PC）formatterのtest
     */
    public function testAsIsMoreSearch()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsMoreSearch(null))->equals('');
        verify($formatter->asIsMoreSearch(0))->equals('最初から表示');
        verify($formatter->asIsMoreSearch(1))->equals('ボタンを押すと表示');
    }

    /**
     * 入力方法（PC）formatterのtest
     */
    public function testAsIsOnTop()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsOnTop(null))->equals('');
        verify($formatter->asIsOnTop(0))->equals('検索一覧にのみ表示');
        verify($formatter->asIsOnTop(1))->equals('トップおよび検索一覧に表示');
    }

    /**
     * 入力方法（PC）formatterのtest
     */
    public function testAsIsIconFlg()
    {
        $formatter = new ProseedsFormatter();
        verify($formatter->asIsIconFlg(null))->equals('表示しない（固定）');
        verify($formatter->asIsIconFlg(0))->equals('表示しない');
        verify($formatter->asIsIconFlg(1))->equals('表示する');
    }
}