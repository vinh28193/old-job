<?php
namespace app\common;

use tests\codeception\unit\fixtures\SiteMasterFixture;
use tests\codeception\unit\fixtures\TenantFixture;
use tests\codeception\unit\fixtures\JobMasterFixture;
use tests\codeception\unit\fixtures\ToolMasterFixture;
use Codeception\Specify;
use tests\codeception\unit\JmTestCase;
use app\models\ToolMaster;

class SiteTest extends JmTestCase
{
    use Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function fixtures()
    {
        return [
            'site_master' => SiteMasterFixture::className(),
            'job_master' => JobMasterFixture::className(),
            'tool_master' => ToolMasterFixture::className(),
        ];
    }

    // tests
    public function testConstruct()
    {
        verify(get_class(\Yii::$app->site))->equals('app\common\Site');
    }

    public function testGetToolMaster()
    {
        $site = \Yii::$app->site;
        $default = 'Bednar, Hudson and Wilkinson';

        $this->specify('TOPページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['top'];
            $tags = [
                'title' => $default,
                'description' => "DESCRIPTION",
                'keywords' => $default,
                'h1' => $default,
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('エリアトップのタグが取得できること', function() use ($site, $default) {
            $area = '異世界';
            $site->toolNo = ToolMaster::TOOLNO_MAP['areatop'];
            $site->areaname = $area;
            $tags = [
                'title' => $area . '｜' . $default,
                'description' => $area . '版',
                'keywords' => $default,
                'h1' => $default,
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('原稿詳細ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['manuscriptDetai'];
            $site->jobNo = 1;
            $tags = [
                'title' => "Alice had never forgotten that, if you could.（ID：1）｜" . $default,
                'description' => "Duchess, it had some kind of thing never.（ID：1）",
                'keywords' => $default,
                'h1' => "Duchess sang the second thing is to do it.'.（ID：1）｜" . $default,
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('携帯に送るページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['sendMobileInput'];
            $site->jobNo = 1;
            $tags = [
                'title' => "携帯に送る（ID：1）｜" . $default,
                'description' => "Rome, and Rome--no, THAT'S all wrong, I'm. | Alice as he spoke. 'A cat may look at it!' This.（ID：1）",
                'keywords' => "I'll be jury,\" Said cunning old Fury: \"I'll try.",
                'h1' => "YOUR adventures.' 'I could tell you what year it. | I'm perfectly sure I don't put my arm round your. | I wish you were down here with me! There are no.",
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('携帯に送る完了ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['sendMobileCompleted'];
            $site->jobNo = 1;
            $tags = [
                'title' => "携帯に送る完了（ID：1）｜" . $default,
                'description' => "It was all dark overhead; before her was another. | There was no 'One, two, three, and away,' but.（ID：1）",
                'keywords' => "King, going up to Alice, that she was quite out. | It's by far the most confusing thing I ever.",
                'h1' => "King: 'however, it may kiss my hand if it.（ID：1）｜Bednar, Hudson and Wilkinson",
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('応募するページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['applicationInput'];
            $site->jobNo = 1;
            $tags = [
                'title' => "Alice had never forgotten that, if you could.（ID：1）に応募する｜" . $default,
                'description' => "I hadn't begun my tea--not above a week or. | Alice. 'Why, there they lay sprawling about,. | There could be no use denying it. I suppose.",
                'keywords' => "White Rabbit. She was close behind her,. | Dodo in an undertone to the executioner: 'fetch.",
                'h1' => "THESE?' said the White Rabbit blew three blasts. | I suppose Dinah'll be sending me on messages.",
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('応募する確認ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['applicationConfirmation'];
            $site->jobNo = 1;
            $tags = [
                'title' => "Alice went on saying to herself 'It's the first.（ID：1）に応募する｜" . $default,
                'description' => "Alice had never forgotten that, if you could. | Alice had been (Before she had someone to listen.",
                'keywords' => "Alice said; but was dreadfully puzzled by the.",
                'h1' => $default,
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('応募する完了ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['applicationCompleted'];
            $site->jobNo = 1;
            $tags = [
                'title' => "Duchess sang the second thing is to do it.'.（ID：1）に応募完了｜" . $default,
                'description' => $default,
                'keywords' => "Duchess, it had some kind of thing never.",
                'h1' => "Alice had never forgotten that, if you could.（ID：1）に応募完了",
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('条件無しの検索結果ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['searchResult'];
            $site->searchname = [];
            $tags = [
                'title' => $default,
                'description' => '',
                'keywords' => $default,
                'h1' => '求人検索結果',
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('条件1つの検索結果ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['searchResultOne'];
            $site->searchname = ['title' => 'foobar'];
            $tags = [
                'title' => 'foobarの求人検索結果｜' .  $default,
                'description' => 'foobar',
                'keywords' => $default,
                'h1' => 'foobarの求人検索結果',
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('条件2つの検索結果ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['searchResultTwo'];
            $site->searchname = ['title' => 'hoge'];
            $tags = [
                'title' => 'hogeの求人検索結果｜' .  $default,
                'description' => 'hogeの検索結果',
                'keywords' => $default,
                'h1' => 'hogeの求人検索結果',
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });

        $this->specify('条件3つ以上の検索結果ページのタグが取得できること', function() use ($site, $default) {
            $site->toolNo = ToolMaster::TOOLNO_MAP['searchResultOther'];
            $site->searchname = ['digest' => 'foo-bar-baz'];
            $tags = [
                'title' => $default,
                'description' => 'foo-bar-bazの検索結果',
                'keywords' => 'foo-bar-baz',
                'h1' => 'foo-bar-baz求人検索結果',
            ];
            $this->checkTags($site->getToolMaster(), $tags);
        });
    }

    protected function checkTags($toolMaster, $tags)
    {
        foreach ($tags as $prop => $expected) {
            verify($toolMaster->{$prop})->equals($expected);
        }
    }
}


