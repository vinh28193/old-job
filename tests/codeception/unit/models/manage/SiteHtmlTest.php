<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use app\models\manage\SiteHtml;
use app\models\manage\HeaderFooterSetting;
use yii;
use yii\web\UploadedFile;

/**
 * Class SiteHtmlTest
 * @package models\manage
 */
class SiteHtmlTest extends JmTestCase
{

//    /** 自明のテストのため不要 */
//    public function testTableName(){}

    /**
     * rulesのテスト
     */
    public function testRules()
    {
        $this->specify('必須チェック', function () {
            $model = new SiteHtml();
            $model->validate();
            verify($model->hasErrors('header_html'))->true();
            verify($model->hasErrors('footer_html'))->true();
        });

        $this->specify('形式チェック:string', function () {
            $model = new SiteHtml();
            $model->header_html = [1, 2];
            $model->footer_html = [1, 2];
            $model->analytics_html = [1, 2];
            $model->conversion_html = [1, 2];
            $model->remarketing_html = [1, 2];
            $model->another_html = [1, 2];
            $model->validate();
            verify($model->hasErrors('header_html'))->true();
            verify($model->hasErrors('footer_html'))->true();
            verify($model->hasErrors('analytics_html'))->true();
            verify($model->hasErrors('conversion_html'))->true();
            verify($model->hasErrors('remarketing_html'))->true();
            verify($model->hasErrors('another_html'))->true();
        });

        $this->specify('文字数チェック', function () {
            $model = new SiteHtml();
            $model->analytics_html = str_repeat('a', 10001);
            $model->conversion_html = str_repeat('a', 10001);
            $model->remarketing_html = str_repeat('a', 10001);
            $model->another_html = str_repeat('a', 10001);
            $model->validate();
            verify($model->hasErrors('analytics_html'))->true();
            verify($model->hasErrors('conversion_html'))->true();
            verify($model->hasErrors('remarketing_html'))->true();
            verify($model->hasErrors('another_html'))->true();
        });

        $this->specify('形式チェック:<script>タグ', function () {
            $model = new SiteHtml();
            $model->analytics_html = '<script>ga("create", "AA-000000-00", "auto");';
            $model->validate();
            verify($model->hasErrors('analytics_html'))->true();
        });

        $this->specify('形式チェック:</script>タグ', function () {
            $model = new SiteHtml();
            $model->analytics_html = 'ga("create", "AA-000000-00", "auto");</script>';
            $model->validate();
            verify($model->hasErrors('analytics_html'))->true();
        });

        $this->specify('形式チェック:analytics.js', function () {
            $model = new SiteHtml();
            $model->analytics_html = 'analytics.js;ga("create", "AA-000000-00", "auto");';
            $model->conversion_html = 'analytics.js;ga("create", "AA-000000-00", "auto");';
            $model->remarketing_html = 'analytics.js;ga("create", "AA-000000-00", "auto");';
            $model->another_html = 'analytics.js;ga("create", "AA-000000-00", "auto");';
            $model->validate();
            verify($model->hasErrors('analytics_html'))->true();
            verify($model->hasErrors('conversion_html'))->true();
            verify($model->hasErrors('remarketing_html'))->true();
            verify($model->hasErrors('another_html'))->true();
        });

        $this->specify('形式チェック:GoogleAnalyticsObject', function () {
            $model = new SiteHtml();
            $model->analytics_html = 'GoogleAnalyticsObject;ga("create", "AA-000000-00", "auto");';
            $model->conversion_html = 'GoogleAnalyticsObject;ga("create", "AA-000000-00", "auto");';
            $model->remarketing_html = 'GoogleAnalyticsObject;ga("create", "AA-000000-00", "auto");';
            $model->another_html = 'GoogleAnalyticsObject;ga("create", "AA-000000-00", "auto");';
            $model->validate();
            verify($model->hasErrors('analytics_html'))->true();
            verify($model->hasErrors('conversion_html'))->true();
            verify($model->hasErrors('remarketing_html'))->true();
            verify($model->hasErrors('another_html'))->true();
        });

        $this->specify('正しい値', function () {
            $model = new SiteHtml();
            $model->header_html = 'aaa';
            $model->footer_html = 'aaa';
            $model->analytics_html = str_repeat('a', 10000);
            $model->conversion_html = str_repeat('a', 10000);
            $model->remarketing_html = str_repeat('a', 10000);
            $model->another_html = str_repeat('a', 10000);
            verify($model->validate())->true();
        });
    }

//    public function testAttributeLabels(){}

    /**
     * fixHtmlのテスト
     */
    public function testFixHtml()
    {
        $this->specify('HeaderFooterモデルに問題があるとき', function () {
            $model = new SiteHtml();
            $ngHd = $this->ngHeaderFooter();
            $model->fixHtml($ngHd);
            verify($model->header_html)->isEmpty();
            verify($model->footer_html)->isEmpty();
        });

        $this->specify('HeaderFooterモデルに問題がないとき', function () {
            $model = new SiteHtml();
            $ngHd = $this->okHeaderFooter();
            $model->fixHtml($ngHd);
            verify($model->header_html)->equals($model->makeHeaderHtml($ngHd));
            verify($model->footer_html)->equals($model->makeFooterHtml($ngHd));
        });
    }

    /**
     * MakeHeaderHtmlのテスト
     */
    public function testMakeHeaderHtml()
    {
        $this->specify('プレビューでない場合', function () {
            $model = new SiteHtml();
            $okHd = $this->okHeaderFooter();
            $model->fixHtml($okHd);
            // 画像URLがリンクパスで表示される。
            verify($model->header_html)->contains(date('Ymd'));
            // header_text、とheader_url、の両方が入力されているリンクは表示される。
            verify($model->header_html)->contains('<li><a href="http://www.pro-seeds.co.jp/?id=header_url1">ああああああ</a></li>');
            verify($model->header_html)->contains('<li><a>いいいいいい</a></li>');
            // header_textが入力されていないリンクは表示されない。
            verify($model->header_html)->notContains('header_url3');
            // 電話番号が表示されている。
            verify($model->header_html)->contains('0123456789');
            // 電話番号テキストが表示されている。
            verify($model->header_html)->contains('お電話でのお問い合わせ');
        });

        $this->specify('プレビューの場合', function () {
            $model = new SiteHtml();
            $prHd = $this->previewHeaderFooter();
            $model->fixHtml($prHd);
            // 画像URLがbase64形式で表示される。
            verify($model->header_html)->notEmpty();
            verify($model->header_html)->contains('data:image/png;base64');
        });

        $this->specify('電話番号が存在しない場合', function () {
            $model = new SiteHtml();
            $okHd = $this->okHeaderFooter();
            $okHd->tel_no = null;
            $model->fixHtml($okHd);
            // 電場番号のタグ自体が表示されていない
            verify($model->header_html)->notEmpty();
            verify($model->header_html)->notContains('fa-phone');
        });

        $this->specify('電話番号もヘッダリンクも存在しない場合はスマホのボタンも表示されない', function () {
            $model = new SiteHtml();
            $okHd = $this->okHeaderFooter();
            $okHd->tel_no = null;
            for ($i = 1; $i <= 10; $i++) {
                $okHd->{'header_text' . $i} = null;
            }
            $model->fixHtml($okHd);
            // 電場番号のタグ自体が表示されていない
            verify($model->header_html)->notContains('<span class="sr-only">Menu</span>');
        });
    }

    /**
     * MakeFooterHtmlのテスト
     */
    public function testMakeFooterHtml()
    {
        $model = new SiteHtml();
        $okHd = $this->okHeaderFooter();
        $model->fixHtml($okHd);
        // footer_textが入力されているリンクは表示される。
        verify($model->footer_html)->contains('<li><a href="http://www.pro-seeds.co.jp/?id=footer_url1">まままままま</a></li>');
        verify($model->footer_html)->contains('<li><a>みみみみみみ</a></li>');
        // footer_text又はfooter_urlのどちらかが入力されていないリンクは表示されない。
        verify($model->footer_html)->notContains('footer_url3');
        // コピーライトが表示されている。
        verify($model->footer_html)->contains('Proseeds');
    }

    /**
     * 'tagLabel'のテスト
     */
    public function testTagLabel()
    {
        $model = new SiteHtml();
        // タグ管理で管理しているタグのプロパティ名に関して、ラベルが紐づいているかの検証
        verify($model->tagLabel('analytics_html'))->equals(Yii::t('app', 'Google Analyticsタグ(全画面の</head>の直前）'));
        verify($model->tagLabel('conversion_html'))->equals(Yii::t('app', '応募コンバージョンタグ(応募完了画面の</body> の直前）'));
        verify($model->tagLabel('remarketing_html'))->equals(Yii::t('app', 'リマーケティングタグ(全画面の(</body> の直前）'));
        verify($model->tagLabel('another_html'))->equals(Yii::t('app', 'その他解析タグ(全画面の</head> の直前）'));

        // タグ管理で管理しているタグのプロパティ名以外の文字列に関して、空文字を返す
        verify($model->tagLabel('aaa'))->isEmpty();
    }

    /**
     * @return HeaderFooterSetting
     */
    private function okHeaderFooter()
    {
        $model = new HeaderFooterSetting();
        $_FILES = [
            $model->formName() => [
                'error' => ['imageFile' => 0],
                'name' => ['imageFile' => "media-upload.png"],
                'size' => ['imageFile' => 512],
                'tmp_name' => ['imageFile' => Yii::getAlias('@app') . '/tests/codeception/_data/media-upload.png'],
                'type' => ['imageFile' => "image/png"],
            ]
        ];
        $params = [
            'tenant_id' => '2',
            'tel_no' => '0123456789',
            'tel_text' => 'お電話でのお問い合わせ',
            'header_text1' => 'ああああああ',
            'header_text2' => 'いいいいいい',
            'header_text3' => '',
            'header_text4' => '',
            'header_text5' => '',
            'header_text6' => '',
            'header_text7' => '',
            'header_text8' => '',
            'header_text9' => '',
            'header_text10' => '',
            'header_url1' => 'http://www.pro-seeds.co.jp/?id=header_url1',
            'header_url2' => '',
            'header_url3' => 'http://www.pro-seeds.co.jp/?id=header_url3',
            'header_url4' => '',
            'header_url5' => '',
            'header_url6' => '',
            'header_url7' => '',
            'header_url8' => '',
            'header_url9' => '',
            'header_url10' => '',
            'footer_text1' => 'まままままま',
            'footer_text2' => 'みみみみみみ',
            'footer_text3' => '',
            'footer_text4' => '',
            'footer_text5' => '',
            'footer_text6' => '',
            'footer_text7' => '',
            'footer_text8' => '',
            'footer_text9' => '',
            'footer_text10' => '',
            'footer_url1' => 'http://www.pro-seeds.co.jp/?id=footer_url1',
            'footer_url2' => '',
            'footer_url3' => 'http://www.pro-seeds.co.jp/?id=footer_url3',
            'footer_url4' => '',
            'footer_url5' => '',
            'footer_url6' => '',
            'footer_url7' => '',
            'footer_url8' => '',
            'footer_url9' => '',
            'footer_url10' => '',
            'copyright' => 'Proseeds',
        ];
        $model->load([$model->formName() => $params]);
        // UploadedFileの初期化
        UploadedFile::reset();
        $model->setImageFile();
        return $model;
    }

    /**
     * @return HeaderFooterSetting
     */
    private function previewHeaderFooter()
    {
        $model = new HeaderFooterSetting();
        $params = [
            'tenant_id' => '2',
            'tel_no' => '0123456789',
            'tel_text' => 'お電話でのお問い合わせ',
            // プレビューの場合、必須チェックよけに入れている
            'logo_file_name' => 'dummy_name.png',
            'header_text1' => 'ああああああ',
            'header_text2' => 'いいいいいい',
            'header_text3' => '',
            'header_text4' => '',
            'header_text5' => '',
            'header_text6' => '',
            'header_text7' => '',
            'header_text8' => '',
            'header_text9' => '',
            'header_text10' => '',
            'header_url1' => 'http://www.pro-seeds.co.jp/?id=header_url1',
            'header_url2' => '',
            'header_url3' => 'http://www.pro-seeds.co.jp/?id=header_url3',
            'header_url4' => '',
            'header_url5' => '',
            'header_url6' => '',
            'header_url7' => '',
            'header_url8' => '',
            'header_url9' => '',
            'header_url10' => '',
            'footer_text1' => 'まままままま',
            'footer_text2' => 'みみみみみみ',
            'footer_text3' => '',
            'footer_text4' => '',
            'footer_text5' => '',
            'footer_text6' => '',
            'footer_text7' => '',
            'footer_text8' => '',
            'footer_text9' => '',
            'footer_text10' => '',
            'footer_url1' => 'http://www.pro-seeds.co.jp/?id=footer_url1',
            'footer_url2' => '',
            'footer_url3' => 'http://www.pro-seeds.co.jp/?id=footer_url3',
            'footer_url4' => '',
            'footer_url5' => '',
            'footer_url6' => '',
            'footer_url7' => '',
            'footer_url8' => '',
            'footer_url9' => '',
            'footer_url10' => '',
            'copyright' => 'Proseeds',
            'base64Url' => 'data:image/png;base64, /AAAAA',
        ];
        $model->load([$model->formName() => $params]);
        return $model;
    }

    /**
     * @return HeaderFooterSetting
     */
    private function ngHeaderFooter()
    {
        UploadedFile::reset();
        $model = new HeaderFooterSetting();
        $params = [
            'tenant_id' => '2',
            'tel_no' => '0123456789',
            'tel_text' => 'お電話でのお問い合わせ',
            'header_text1' => 'ああああああ',
            'header_text2' => 'いいいいいい',
            'header_text3' => '',
            'header_text4' => '',
            'header_text5' => '',
            'header_text6' => '',
            'header_text7' => '',
            'header_text8' => '',
            'header_text9' => '',
            'header_text10' => '',
            'header_url1' => 'http://www.pro-seeds.co.jp/?id=header_url1',
            'header_url2' => '',
            'header_url3' => 'http://www.pro-seeds.co.jp/?id=header_url3',
            'header_url4' => '',
            'header_url5' => '',
            'header_url6' => '',
            'header_url7' => '',
            'header_url8' => '',
            'header_url9' => '',
            'header_url10' => '',
            'footer_text1' => 'まままままま',
            'footer_text2' => 'みみみみみみ',
            'footer_text3' => '',
            'footer_text4' => '',
            'footer_text5' => '',
            'footer_text6' => '',
            'footer_text7' => '',
            'footer_text8' => '',
            'footer_text9' => '',
            'footer_text10' => '',
            'footer_url1' => 'http://www.pro-seeds.co.jp/?id=footer_url1',
            'footer_url2' => '',
            'footer_url3' => 'http://www.pro-seeds.co.jp/?id=footer_url3',
            'footer_url4' => '',
            'footer_url5' => '',
            'footer_url6' => '',
            'footer_url7' => '',
            'footer_url8' => '',
            'footer_url9' => '',
            'footer_url10' => '',
            'copyright' => 'Proseeds',
            'imageFile' => '',
        ];
        $model->load([$model->formName() => $params]);
        // UploadedFileの初期化
        UploadedFile::reset();
        $model->setImageFile();
        return $model;
    }
}