<?php
namespace tests\modules\manage\models\forms;

use app\commands\components\Uploader;
use app\common\Helper\JmUtils;
use app\models\FreeContentElement;
use app\modules\manage\models\forms\FreeContentElementForm;
use tests\codeception\unit\JmTestCase;
use Yii;
use yii\web\UploadedFile;

/**
 * Class FreeContentElementFormTest
 * @package tests\modules\manage\models\forms
 * @see https://github.com/proseeds/jobmaker/wiki/FreeContentElementForm%E3%81%AB%E3%81%A4%E3%81%84%E3%81%A6
 */
class FreeContentElementFormTest extends JmTestCase
{
    /**
     * rulesのtest
     */
    public function testRules()
    {
        // 最初にキャッシュしておく
        FreeContentElementForm::cacheExistingFileNames();

        $this->specify('typeとsortはloadできない', function () {
            $model = new FreeContentElementForm();
            $model->load([
                'type' => 1,
                'sort' => 2,
            ], '');
            verify($model->type)->isEmpty();
            verify($model->sort)->isEmpty();
        });

        $this->specify('必須の検証', function () {
            $model = new FreeContentElementForm();
            $model->validate();
            verify($model->hasErrors('displayItem'))->true();
            verify($model->hasErrors('layout'))->true();
            verify($model->hasErrors('type'))->true();
            verify($model->hasErrors('sort'))->true();
        });

        $this->specify('displayItemとlayoutの値の検証', function () {
            $model = new FreeContentElementForm();

            $model->displayItem = 4;
            $model->layout = 3;
            $model->validate();
            verify($model->hasErrors('displayItem'))->true();
            verify($model->hasErrors('layout'))->true();

            $model->displayItem = 'test';
            $model->layout = [1, 2];
            $model->validate();
            verify($model->hasErrors('displayItem'))->true();
            verify($model->hasErrors('layout'))->true();
        });

        $this->specify('文字列の検証', function () {
            $model = new FreeContentElementForm();

            $model->base64Img = ['test'];
            $model->image_file_name = 123;
            $model->validate();
            verify($model->hasErrors('base64Img'))->true();
            verify($model->hasErrors('image_file_name'))->true();

            $model->image_file_name = str_repeat('a', 256);
            $model->imgFile = true;
            $model->validate();
            verify($model->hasErrors('image_file_name'))->true();
        });

        /**
         * @see https://github.com/proseeds/jobmaker/wiki/FreeContentElementForm%E3%81%AB%E3%81%A4%E3%81%84%E3%81%A6
         */
        $this->specify('image_file_nameの検証(正常値含)', function () {
            $notExists = str_repeat('a', 255);
            $exists = FreeContentElement::find()->select('image_file_name')->scalar();

            $displayItemArray = [
                FreeContentElementForm::DISPLAY_TEXT,
                FreeContentElementForm::DISPLAY_IMG,
                FreeContentElementForm::DISPLAY_BOTH,
            ];

            $imgFileArray = [
                true, // 本当は入るのはUploadedFileのインスタンスだが、動作に影響ないのでtrueで代用
                null,
            ];

            $imageFileNameArray = [
                $notExists,
                $exists,
                '',
            ];

            foreach ($displayItemArray as $displayItem) {
                foreach ($imgFileArray as $imgFile) {
                    foreach ($imageFileNameArray as $imageFileName) {
                        $this->verifyImageFileName($displayItem, $imgFile, $imageFileName);
                    }
                }
            }
        });

        $this->specify('textの検証(正常値含)', function () {
            $model = new FreeContentElementForm();
            $displayItemArray = [
                FreeContentElementForm::DISPLAY_TEXT,
                FreeContentElementForm::DISPLAY_IMG,
                FreeContentElementForm::DISPLAY_BOTH,
            ];

            // 画像のみの時以外は必須
            foreach ($displayItemArray as $displayItem) {
                $model->displayItem = $displayItem;
                $model->validate();
                if ($displayItem == FreeContentElementForm::DISPLAY_IMG) {
                    verify($model->hasErrors('text'))->false();
                } else {
                    verify($model->hasErrors('text'))->true();
                }
            }

            // 画像のみの時以外は最大5000字
            $just = str_repeat('a', 5000);
            foreach ($displayItemArray as $displayItem) {
                $model->displayItem = $displayItem;
                $model->text = $just;
                $model->validate();
                verify($model->hasErrors('text'))->false();

                $model->text = $just . 'a';
                $model->validate();
                if ($displayItem == FreeContentElementForm::DISPLAY_IMG) {
                    verify($model->hasErrors('text'))->false();
                } else {
                    verify($model->hasErrors('text'))->true();
                }
            }

            // いつでも文字列
            foreach ($displayItemArray as $displayItem) {
                $model->displayItem = $displayItem;
                $model->text = [1, 2];
                $model->validate();
                verify($model->hasErrors('text'))->true();
            }
        });


        $this->specify('imgFileの検証', function () {
            $model = new FreeContentElementForm();
            $displayItemArray = [
                FreeContentElementForm::DISPLAY_TEXT,
                FreeContentElementForm::DISPLAY_IMG,
                FreeContentElementForm::DISPLAY_BOTH,
            ];

            // テキストのみの時以外は必須チェック
            foreach ($displayItemArray as $displayItem) {
                $model->displayItem = $displayItem;
                $model->validate();
                if ($displayItem == FreeContentElementForm::TYPE_ONLY_TEXT) {
                    verify($model->hasErrors('imgFile'))->false();
                } else {
                    verify($model->hasErrors('imgFile'))->true();
                }
            }

            $_FILES = [
                'csv' => [
                    'name' => 'test.csv',
                    'type' => 'application/vnd.ms-excel',
                    'tmp_name' => Yii::getAlias('@app') . '/tests/codeception/_data/test.png',
                    'size' => 512,
                    'error' => 0,
                ],
                'tooBig' => [
                    'name' => 'tooBig.png',
                    'type' => 'image/jpg',
                    'tmp_name' => Yii::getAlias('@app') . '/tests/codeception/_data/test.png',
                    'size' => FreeContentElement::MAX_SIZE + 1,
                    'error' => 0,
                ],
                'just' => [
                    'name' => 'just.png',
                    'type' => 'image/jpg',
                    'tmp_name' => Yii::getAlias('@app') . '/tests/codeception/_data/test.png',
                    'size' => FreeContentElement::MAX_SIZE,
                    'error' => 0,
                ],
            ];


            // テキストのみの時以外はファイルチェック
            foreach ($displayItemArray as $displayItem) {
                $model->displayItem = $displayItem;

                // 拡張子違う
                $model->imgFile = UploadedFile::getInstanceByName('csv');
                $model->validate();
                if ($displayItem == FreeContentElementForm::TYPE_ONLY_TEXT) {
                    verify($model->hasErrors('imgFile'))->false();
                } else {
                    verify($model->hasErrors('imgFile'))->true();
                }

                // サイズオーバー
                $model->imgFile = UploadedFile::getInstanceByName('tooBig');
                $model->validate();
                if ($displayItem == FreeContentElementForm::TYPE_ONLY_TEXT) {
                    verify($model->hasErrors('imgFile'))->false();
                } else {
                    verify($model->hasErrors('imgFile'))->true();
                }

                // 正常値
                $model->imgFile = UploadedFile::getInstanceByName('just');
                $model->validate();
                verify($model->hasErrors('imgFile'))->false();
            }
        });

        $this->specify('正常値', function () {
            $model = new FreeContentElementForm();
            $model->load([
                'displayItem' => FreeContentElementForm::DISPLAY_BOTH,
                'layout' => FreeContentElementForm::IMG_IS_LEFT,
                'text' => str_repeat('y', 5000),
                'image_file_name' => str_repeat('j', 255),
                'imgFile' => true, // 本当は入るのはUploadedFileのインスタンスだが、動作に影響ないのでtrueで代用
            ], '');
            $model->sort = 1;
            verify($model->validate())->true();
        });
    }

    // attributeLabelsはテストするまでも無いため省略

    /**
     * loadのtest
     */
    public function testLoad()
    {
        $text = 'test text';

        // テキストのみ
        $model = new FreeContentElementForm();
        $model->load([
            'displayItem' => FreeContentElementForm::DISPLAY_TEXT,
            'text' => $text,
        ], '');
        verify($model->type)->equals(FreeContentElementForm::TYPE_ONLY_TEXT);
        verify($model->text)->equals($text);

        // 画像のみ
        $model = new FreeContentElementForm();
        $model->load([
            'displayItem' => FreeContentElementForm::DISPLAY_IMG,
            'text' => $text,
        ], '');
        verify($model->type)->equals(FreeContentElementForm::TYPE_ONLY_IMG);
        verify($model->text)->isEmpty();

        // 両方で画像が左
        $model = new FreeContentElementForm();
        $model->load([
            'displayItem' => FreeContentElementForm::DISPLAY_BOTH,
            'layout' => FreeContentElementForm::IMG_IS_LEFT,
            'text' => $text,
        ], '');
        verify($model->type)->equals(FreeContentElementForm::TYPE_LEFT_IMG);
        verify($model->text)->equals($text);

        // 両方で画像が右
        $model = new FreeContentElementForm();
        $model->load([
            'displayItem' => FreeContentElementForm::DISPLAY_BOTH,
            'layout' => FreeContentElementForm::TEXT_IS_LEFT,
            'text' => $text,
        ], '');
        verify($model->type)->equals(FreeContentElementForm::TYPE_LEFT_TEXT);
        verify($model->text)->equals($text);
    }

    /**
     * loadAllのtest
     * loadとloadFileInfoのテストがあるので
     * sort部分のみテストする
     */
    public function testLoadAll()
    {
        $model = new FreeContentElementForm();
        verify($model->loadAll(7, [], ''))->false();
        verify($model->sort)->isEmpty();

        $_FILES = [
            $model->formName() => [
                'name' => [6 => ['imgFile' => 'test.png']],
                'type' => [6 => ['imgFile' => 'image/jpg']],
                'tmp_name' => [6 => ['imgFile' => Yii::getAlias('@app') . '/tests/codeception/_data/test.png']],
                'size' => [6 => ['imgFile' => 512]],
                'error' => [6 => ['imgFile' => 0]],
            ],
        ];

        verify($model->loadAll(7, ['displayItem' => FreeContentElementForm::DISPLAY_BOTH], ''))->true();
        verify($model->imgFile)->isInstanceOf(UploadedFile::className());
    }

    /**
     * recommendedWidthのtest
     */
    public function testRecommendedWidth()
    {
        $model = new FreeContentElementForm();
        $model->displayItem = FreeContentElementForm::DISPLAY_TEXT;
        verify($model->recommendedWidth())->equals(0);

        $model->displayItem = FreeContentElementForm::DISPLAY_IMG;
        verify($model->recommendedWidth())->equals(FreeContentElementForm::WIDTH_ONLY_IMG);

        $model->displayItem = FreeContentElementForm::DISPLAY_BOTH;
        verify($model->recommendedWidth())->equals(FreeContentElementForm::WIDTH_BOTH);
    }

    /**
     * cacheExistingFileNamesとexistingFileNamesのtest
     */
    public function testCacheExistingFileNames()
    {
        FreeContentElementForm::cacheExistingFileNames();
        $model = new FreeContentElementForm();
        $existingFileNames = static ::method($model, 'existingFileNames', []);
        foreach (FreeContentElement::find()->imageFileNames() as $fileName) {
            verify(in_array($fileName, $existingFileNames))->true();
        }
    }

    /**
     * isRequiredTextとisRequiredImageのtest
     */
    public function testIsRequired()
    {
        $model = new FreeContentElementForm();

        $model->displayItem = FreeContentElementForm::DISPLAY_TEXT;
        verify(static ::method($model, 'isRequiredText', []))->true();
        verify(static ::method($model, 'isRequiredImage', []))->false();

        $model->displayItem = FreeContentElementForm::DISPLAY_IMG;
        verify(static ::method($model, 'isRequiredText', []))->false();
        verify(static ::method($model, 'isRequiredImage', []))->true();

        $model->displayItem = FreeContentElementForm::DISPLAY_BOTH;
        verify(static ::method($model, 'isRequiredText', []))->true();
        verify(static ::method($model, 'isRequiredImage', []))->true();
    }

    /**
     * imgFormAttributeのtest
     */
    public function testImgFormAttribute()
    {
        $model = new FreeContentElementForm();
        $model->sort = 11;
        verify(static::method($model, 'imgFormAttribute', []))->equals('[10]imgFile');
    }

    // displayItemLabelsとlayoutLabelsはテストするまでも無いため省略

    /**
     * pluginOptionsForInitのtest
     */
    public function testPluginOptionsForInit()
    {
        $displayItems = array_keys(FreeContentElementForm::displayItemLabels());

        // 新規作成
        $model = new FreeContentElementForm();
        foreach ($displayItems as $item) {
            $model->displayItem = $item;
            verify($model->pluginOptionsForInit())->equals([]);
        }

        // 更新、コピー
        /** @var FreeContentElementForm $model */
        $model = FreeContentElementForm::find()->one();
        foreach ($displayItems as $item) {
            $model->displayItem = $item;
            if ($item == FreeContentElementForm::DISPLAY_TEXT) {
                verify($model->pluginOptionsForInit())->equals([]);
            } else {
                verify($model->pluginOptionsForInit())->equals([
                    'initialPreview' => [$model->srcUrl()],
                    'initialPreviewAsData' => true,
                ]);
            }
        }
    }

    /**
     * loadFileInfoのtest
     */
    public function testLoadFileInfo()
    {
        $imageFileName = 'testImageFileName.jpg';
        $model = new FreeContentElementForm();
        $_FILES = [
            $model->formName() => [
                'name' => [10 => ['imgFile' => 'test.png']],
                'type' => [10 => ['imgFile' => 'image/jpg']],
                'tmp_name' => [10 => ['imgFile' => Yii::getAlias('@app') . '/tests/codeception/_data/test.png']],
                'size' => [10 => ['imgFile' => 512]],
                'error' => [10 => ['imgFile' => 0]],
            ],
        ];


        // 画像の無いタイプの場合はファイル名クリア
        $model->displayItem = FreeContentElementForm::DISPLAY_TEXT;
        $model->image_file_name = $imageFileName;
        $model->sort = 1;
        $model->loadFileInfo();
        verify($model->image_file_name)->isEmpty();

        $model->image_file_name = $imageFileName;

        // 画像のあるタイプでpost画像がある場合はファイル名生成
        $model->displayItem = FreeContentElementForm::DISPLAY_BOTH;
        $model->image_file_name = $imageFileName;
        $model->sort = 11;
        $model->loadFileInfo();
        verify($model->image_file_name)->notEquals($imageFileName);

        // 画像のあるタイプで、post画像が無く、モデルが新規でない場合はファイル名を保持
        /** @var FreeContentElementForm $model */
        $model = FreeContentElementForm::find()->one();
        $model->displayItem = FreeContentElementForm::DISPLAY_IMG;
        $model->image_file_name = $imageFileName;
        $model->sort = 10;
        $model->loadFileInfo();
        verify($model->image_file_name)->equals($model->getOldAttribute('image_file_name'));
    }

    /**
     * saveFileのtest
     */
    public function testSaveFile()
    {
        $imageFileName = 'testImageFileName.jpg';
        $model = new FreeContentElementForm();
        $_FILES = [
            $model->formName() => [
                'name' => [10 => ['imgFile' => 'test.png']],
                'type' => [10 => ['imgFile' => 'image/jpg']],
                'tmp_name' => [10 => ['imgFile' => Yii::getAlias('@app') . '/tests/codeception/_data/test.png']],
                'size' => [10 => ['imgFile' => 512]],
                'error' => [10 => ['imgFile' => 0]],
            ],
        ];
        $uploader = new Uploader();
        $uploader->dirPath = FreeContentElement::DIR_PATH;

        // 画像の無いタイプはスルーしてfalse
        $model->displayItem = FreeContentElementForm::DISPLAY_TEXT;
        $model->image_file_name = $imageFileName;
        $model->sort = 11;
        verify($model->saveFile())->true();
        verify($uploader->hasStorage($imageFileName))->false();

        // 画像が必要なタイプでpostファイルが無い時
        $model->displayItem = FreeContentElementForm::DISPLAY_BOTH;
        $model->sort = 9;
        // ファイル名がある（更新やコピーで画像の変更をしなかった）
        $model->image_file_name = $imageFileName;
        verify($model->saveFile())->true();
        verify($uploader->hasStorage($imageFileName))->false();
        // ファイル名も無い（何らかの不正な操作）
        $model->image_file_name = '';
        verify($model->saveFile())->false();
        verify($uploader->hasStorage($imageFileName))->false();

        // 画像が必要なタイプでpostファイルがある時（画像を登録・更新した）
        $model->displayItem = FreeContentElementForm::DISPLAY_BOTH;
        $model->image_file_name = $imageFileName;
        $model->sort = 11;
        verify($model->saveFile())->true();
        verify($uploader->hasStorage($imageFileName))->true();

        // 消しておく
        $uploader->deleteStorageFile($imageFileName);
    }

    /**
     * srcUrlのtest
     */
    public function testSrcUrl()
    {
        $model = new FreeContentElementForm();
        $model->image_file_name = 'fileName';
        verify($model->srcUrl())->equals(JmUtils::fileUrl('free-content/fileName?public=1'));
        $model->base64Img = 'base64Img';
        verify($model->srcUrl())->equals('base64Img');
    }

    // displayItemLabelsとlayoutLabelsはテストするまでも無いため省略

    /**
     * displayItemのtest
     */
    public function testDisplayItem()
    {
        $model = new FreeContentElementForm();

        $model->type = FreeContentElement::TYPE_ONLY_TEXT;
        verify($model->displayItem)->equals(FreeContentElementForm::DISPLAY_TEXT);

        $model->displayItem = null;
        $model->type = FreeContentElement::TYPE_ONLY_IMG;
        verify($model->displayItem)->equals(FreeContentElementForm::DISPLAY_IMG);

        $model->displayItem = null;
        $model->type = FreeContentElement::TYPE_LEFT_IMG;
        verify($model->displayItem)->equals(FreeContentElementForm::DISPLAY_BOTH);

        $model->displayItem = null;
        $model->type = FreeContentElement::TYPE_LEFT_TEXT;
        verify($model->displayItem)->equals(FreeContentElementForm::DISPLAY_BOTH);

        $model->displayItem = 100;
        verify($model->displayItem)->equals(100);
    }

    /**
     * layoutのtest
     */
    public function testLayout()
    {
        $model = new FreeContentElementForm();

        $model->type = FreeContentElement::TYPE_ONLY_TEXT;
        verify($model->layout)->null();

        $model->type = FreeContentElement::TYPE_ONLY_IMG;
        verify($model->layout)->null();

        $model->type = FreeContentElement::TYPE_LEFT_IMG;
        verify($model->layout)->equals(FreeContentElementForm::IMG_IS_LEFT);

        $model->layout = null;
        $model->type = FreeContentElement::TYPE_LEFT_TEXT;
        verify($model->layout)->equals(FreeContentElementForm::TEXT_IS_LEFT);

        $model->layout = 100;
        verify($model->layout)->equals(100);
    }

    /**
     * typeFromInputのtest
     */
    public function testTypeFromInput()
    {
        $model = new FreeContentElementForm();

        $model->displayItem = FreeContentElementForm::DISPLAY_TEXT;
        $model->layout = FreeContentElementForm::TEXT_IS_LEFT;
        verify(static::method($model, 'typeFromInput', []))->equals(FreeContentElement::TYPE_ONLY_TEXT);


        $model->displayItem = FreeContentElementForm::DISPLAY_IMG;
        $model->layout = FreeContentElementForm::IMG_IS_LEFT;
        verify(static::method($model, 'typeFromInput', []))->equals(FreeContentElement::TYPE_ONLY_IMG);

        $model->displayItem = FreeContentElementForm::DISPLAY_BOTH;
        $model->layout = FreeContentElementForm::IMG_IS_LEFT;
        verify(static::method($model, 'typeFromInput', []))->equals(FreeContentElement::TYPE_LEFT_IMG);

        $model->displayItem = FreeContentElementForm::DISPLAY_BOTH;
        $model->layout = FreeContentElementForm::TEXT_IS_LEFT;
        verify(static::method($model, 'typeFromInput', []))->equals(FreeContentElement::TYPE_LEFT_TEXT);
    }

    // todo 今回は省略するが、事後に保守対応
    /**
     * loadMultipleAndIndexのtest
     */
//    public function testLoadMultipleAndIndex()
//    {
//
//    }

    /**
     * saveMultipleのtest
     */
//    public function testSaveMultiple()
//    {
//
//    }

    /**
     * deleteUnusedFilesByNameのtest
     */
//    public function testDeleteUnusedFiles()
//    {
//
//    }

    /**
     * @param $displayItem
     * @param $imgFile
     * @param $imageFileName
     */
    private function verifyImageFileName($displayItem, $imgFile, $imageFileName)
    {
        $model = new FreeContentElementForm();
        $model->displayItem = $displayItem;
        $model->imgFile = $imgFile;
        $model->image_file_name = $imageFileName;
        $model->validate();

        // 画像のないタイプの時
        if ($displayItem === FreeContentElementForm::DISPLAY_TEXT) {
            if (!$imgFile && !$imageFileName) {
                // 両方空の時のみvalidation成功
                verify($model->hasErrors('image_file_name'))->false();
            } else {
                verify($model->hasErrors('image_file_name'))->true();
            }
            return;
        }

        // 画像のあるタイプの時
        // ファイル名が無いとvalidation失敗
        if (!$imageFileName) {
            verify($model->hasErrors('image_file_name'))->true();
            return;
        }

        // 画像のあるタイプでファイル名があるとき
        $exists = (FreeContentElement::findOne(['image_file_name' => $imageFileName])) ? true : false;

        // postファイルありでファイル名が既存でないなら通る
        if ($imgFile && !$exists) {
            verify($model->hasErrors('image_file_name'))->false();
            // 既存でなくても文字列チェックが通らないとエラー
            $model->image_file_name = str_repeat('a', 256);
            $model->validate();
            verify($model->hasErrors('image_file_name'))->true();
            $model->image_file_name = 123456;
            $model->validate();
            verify($model->hasErrors('image_file_name'))->true();
            return;
        }

        // postファイル無しでファイル名が既存なら通る(ファイル名既存で文字列チェックに引っかかることはありえ無い)
        if (!$imgFile && $exists) {
            verify($model->hasErrors('image_file_name'))->false();
            return;
        }
        // それ以外はエラー
        verify($model->hasErrors('image_file_name'))->true();
    }
}
