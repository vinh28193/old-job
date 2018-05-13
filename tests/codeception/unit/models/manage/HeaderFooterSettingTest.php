<?php
namespace models\manage;

use tests\codeception\unit\JmTestCase;
use app\models\manage\HeaderFooterSetting;
use yii;
use yii\web\UploadedFile;

/**
 * HeaderFooterSettingTestのテスト
 */
class HeaderFooterSettingTest extends JmTestCase
{
    /**
     * テーブルテスト
     */
    public function testTableName()
    {
        verify(HeaderFooterSetting::tableName())->equals('header_footer');
    }

    // initのtestは非常に単純な処理のため省略

    /**
     * ルールテスト
     */
    public function testRules()
    {
        $this->specify('ロゴファイル名validation検証', function () {
            $model = new HeaderFooterSetting();
            // 必須検証
            $model->validate();
            verify($model->hasErrors('logo_file_name'))->true();
            // 文字列検証
            $model->logo_file_name = 1;
            $model->validate();
            verify($model->hasErrors('logo_file_name'))->true();
            // 文字数上限検証
            $model->logo_file_name = str_repeat('a', 201);
            $model->validate();
            verify($model->hasErrors('logo_file_name'))->true();
        });

        $this->specify('imageFile検証', function () {
            $model = new HeaderFooterSetting();
            // 違う拡張子
            $model->imageFile = 'aaa';
            $_FILES = [
                (new HeaderFooterSetting)->formName() => [
                    'error' => ['imageFile' => 0],
                    'name' => ['imageFile' => 'media-upload.png'],
                    'size' => ['imageFile' => 512],
                    'tmp_name' => ['imageFile' => Yii::getAlias('@app') . '/tests/codeception/_data/job.csv'],
                    'type' => ['imageFile' => 'image/png'],
                ],
            ];
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->validate();
            verify($model->hasErrors('imageFile'))->true();
            // サイズオーバー
            $_FILES = [
                (new HeaderFooterSetting)->formName() => [
                    'error' => ['imageFile' => 0],
                    'name' => ['imageFile' => 'media-upload.png'],
                    'size' => ['imageFile' => HeaderFooterSetting::MAX_SIZE + 1],
                    'tmp_name' => ['imageFile' => Yii::getAlias('@app') . '/tests/codeception/_data/media-upload.png'],
                    'type' => ['imageFile' => 'image/png'],
                ],
            ];
            UploadedFile::reset();
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $model->validate();
            verify($model->hasErrors('imageFile'))->true();
        });

        $this->specify('電話番号のvalidation検証', function () {
            $model = new HeaderFooterSetting();
            // 書式検証
            $model->tel_no = '文字列';
            $model->validate();
            verify($model->hasErrors('tel_no'))->true();
            // 文字数上限検証」
            $model = $this->hdModel();
            $model->tel_no = '12345678901234567890-1234568790'; // 31文字
            $model->validate();
            verify($model->hasErrors('tel_no'))->true();
        });

        $this->specify('リンクテキストvalidationの検証', function () {
            $model = new HeaderFooterSetting();
            for ($i = 1; $i <= 10; $i++) {
                // 文字列検証
                $model->{'header_text' . $i} = ['array'];
                $model->{'footer_text' . $i} = 1;
                $model->validate();
                verify($model->hasErrors('header_text' . $i))->true();
                verify($model->hasErrors('footer_text' . $i))->true();
                // 文字数上限検証
                $model->{'header_text' . $i} = str_repeat('a', 21);
                $model->{'footer_text' . $i} = str_repeat('a', 21);
                $model->validate();
                verify($model->hasErrors('header_text' . $i))->true();
                verify($model->hasErrors('footer_text' . $i))->true();
            }
        });

        $this->specify('リンクURLのURL書式検証', function () {
            $model = new HeaderFooterSetting();
            for ($i = 1; $i <= 10; $i++) {
                $model->{'header_url' . $i} = 1;
                $model->{'footer_url' . $i} = '文字列';
                $model->validate();
                verify($model->hasErrors('header_url' . $i))->true();
                verify($model->hasErrors('footer_url' . $i))->true();
            }
        });

        $this->specify('copyrightのvalidation検証', function () {
            $model = new HeaderFooterSetting();
            // 文字列検証
            $model->copyright = 1;
            $model->validate();
            verify($model->hasErrors('copyright'))->true();
            // 文字数上限検証
            $model->copyright = str_repeat('a', 201);
            $model->validate();
            verify($model->hasErrors('copyright'))->true();
        });

        $this->specify('tel_textのvalidation検証', function () {
            $model = new HeaderFooterSetting();
            // 文字列検証
            $model->tel_text = 1;
            $model->validate();
            verify($model->hasErrors('tel_text'))->true();
            // 文字数上限検証
            $model->tel_text = str_repeat('a', 51);
            $model->validate();
            verify($model->hasErrors('tel_text'))->true();
        });

        $this->specify('base64Urlのvalidation検証', function () {
            $model = new HeaderFooterSetting();
            // 文字列検証
            $model->base64Url = 1;
            $model->validate();
            verify($model->hasErrors('base64Url'))->true();
        });

        $this->specify('正常な入力値の検証', function () {
            $model = $this->hdModel();
            $model->validate();
            verify($model->validate())->true();
        });
    }

    /**
     * 要素テスト
     */
    public function testAttributeLabels()
    {
        $model = new HeaderFooterSetting();
        verify($model->attributeLabels())->notEmpty();
    }

    /**
     * @return HeaderFooterSetting
     */
    private function hdModel()
    {
        // UploadedFileのキャッシュを削除
        UploadedFile::reset();

        $model = new HeaderFooterSetting();
        $_FILES = [
            (new HeaderFooterSetting)->formName() => [
                'error' => ['imageFile' => 0],
                'name' => ['imageFile' => 'media-upload.png'],
                'size' => ['imageFile' => 512],
                'tmp_name' => ['imageFile' => Yii::getAlias('@app') . '/tests/codeception/_data/media-upload.png'],
                'type' => ['imageFile' => 'image/png'],
            ],
        ];
        $params = [
            'tenant_id' => '2',
            'logo_file_name' => str_repeat('a', 26) . '.png',
            'tel_no' => str_repeat(0, 30),
            'tel_text' => str_repeat('ん', 50),
            'header_text1' => str_repeat('あ', 20),
            'header_text2' => str_repeat('い', 20),
            'header_text3' => str_repeat('う', 20),
            'header_text4' => str_repeat('え', 20),
            'header_text5' => str_repeat('お', 20),
            'header_text6' => str_repeat('か', 20),
            'header_text7' => str_repeat('き', 20),
            'header_text8' => str_repeat('く', 20),
            'header_text9' => str_repeat('け', 20),
            'header_url1' => 'http://www.yahoo.co.jp',
            'header_url2' => 'https://www.google.co.jp/',
            'header_url3' => 'https://www.goo.ne.jp/',
            'header_url4' => 'https://mail.goo.ne.jp',
            'header_url5' => 'http://www.goo-net.com/',
            'header_url6' => 'http://www.goo-net.com/catalog/',
            'header_url7' => 'https://qiita.com/',
            'header_url8' => 'https://github.com/',
            'header_url9' => 'http://linux.just4fun.biz/',
            'header_url10' => '/manual/ja/index.php',
            'footer_text1' => str_repeat('あ', 20),
            'footer_text2' => str_repeat('い', 20),
            'footer_text3' => str_repeat('う', 20),
            'footer_text4' => str_repeat('え', 20),
            'footer_text5' => str_repeat('お', 20),
            'footer_text6' => str_repeat('か', 20),
            'footer_text7' => str_repeat('き', 20),
            'footer_text8' => str_repeat('く', 20),
            'footer_text9' => str_repeat('け', 20),
            'footer_text10' => str_repeat('こ', 20),
            'footer_url1' => 'http://www.yahoo.co.jp',
            'footer_url2' => 'https://www.google.co.jp/',
            'footer_url3' => 'https://www.goo.ne.jp/',
            'footer_url4' => 'https://mail.goo.ne.jp',
            'footer_url5' => 'http://www.goo-net.com/',
            'footer_url6' => 'http://www.goo-net.com/catalog/',
            'footer_url7' => 'https://qiita.com/',
            'footer_url8' => 'https://github.com/',
            'footer_url9' => 'http://linux.just4fun.biz/',
            'footer_url10' => '/manual/ja/index.php',
            'copyright' => str_repeat('a', 50),
        ];
        $model->load([$model->formName() => $params]);
        return $model;
    }
}
