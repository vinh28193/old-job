<?php
namespace test\modules\requests;

use app\commands\components\Uploader;
use app\components\Area;
use app\models\manage\MainVisual;
use app\models\manage\MainVisualImage;
use app\models\manage\WidgetData;
use app\modules\manage\models\requests\MainVisualImageForm;
use tests\codeception\unit\JmTestCase;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

/**
 * Class ManagerTest
 * todo 足りないテストメソッド追加
 */
class ManagerTest extends JmTestCase
{
    const ATTRIBUTES = [
        'url' => 'https://github.com',
        'url_sp' => 'https://github.com/proseeds/jobmaker/pull/1688',
        'content' => 'content',
        'sort' => 44,
        'valid_chk' => 1,
    ];

    /**
     * constructのtest
     * 一緒にformNameもテストする
     * ここではeditorの初期化のテストはしない
     */
    public function testConstruct()
    {
        $attributes = array_merge(['id' => 99, 'memo' => 'memo'], self::ATTRIBUTES);
        $mainVisual = new MainVisual();
        $mainVisualImage = new MainVisualImage($attributes);
        $area = new Area();
        $key = '0_' . $area->firstArea->id;

        // key無し
        $mainVisualImageForm = new MainVisualImageForm($mainVisual, $mainVisualImage);
        verify($mainVisualImageForm->mainVisual)->equals($mainVisual);
        verify($mainVisualImageForm->mainVisualImage)->equals($mainVisualImage);
        foreach ($attributes as $name => $value) {
            verify($mainVisualImageForm->$name)->equals($value);
        }
        verify($mainVisualImageForm->isPublic)->true();

        verify($mainVisualImageForm->formName())->equals('MainVisualImageForm');

        // 他も初期化
        $mainVisualImageForm = new MainVisualImageForm($mainVisual, $mainVisualImage, $key);
        verify($mainVisualImageForm->formName())->equals('MainVisualImageForm' . $key);
    }

    /**
     * 新規作成時に画像が無い場合の例外テスト
     */
    public function testSavingNoImageException()
    {
        $this->expectException(BadRequestHttpException::class);
        $model = $this->formModel(true);
        $model->save();
    }

    /**
     * レコード登録失敗時の例外テスト
     */
    public function testSavingInvalidValueException()
    {
        $this->expectException(BadRequestHttpException::class);
        $model = $this->formModel(true);
        $this->loadFilePost($model->formName(), 'file');
        $model->file = UploadedFile::getInstance($model->mainVisualImage, 'file');
        $model->save();
    }

    public function testSave()
    {
        static::getFixtureInstance('main_visual_image')->unload();
        static::getFixtureInstance('main_visual')->initTable();
        static::getFixtureInstance('main_visual_image')->load();

        $this->setIdentity('owner_admin');

        $this->specify('新規、画像あり', function () {
            // 新規、スマホ画像ありを再現
            $model = $this->formModel(true);
            $this->load($model, 'file');
            $model->save();
            $this->verifySave($model, 'file_name');
            // ファイルの削除
            $this->uploader()->deleteStorageFile($model->mainVisualImage->file_name);
        });

        $this->specify('更新、画像あり', function () {
            // 更新、スマホ画像ありを再現
            $model = $this->formModel(false);
            $this->load($model, 'file_sp');
            $model->save();
            $this->verifySave($model, 'file_name_sp');
            // ファイルの削除
            $this->uploader()->deleteStorageFile($model->mainVisualImage->file_name);
            $this->uploader()->deleteStorageFile($model->mainVisualImage->file_name_sp);
        });

        $this->specify('更新、画像無し', function () {
            // 更新、スマホ画像ありを再現
            $model = $this->formModel(false);
            $this->loadFilePost('', '');
            $model->load([$model->formName() => self::ATTRIBUTES]);
            $model->save();
            $this->verifySave($model, 'file_name_sp');
            // ファイルの削除
            $this->uploader()->deleteStorageFile($model->mainVisualImage->file_name);
            $this->uploader()->deleteStorageFile($model->mainVisualImage->file_name_sp);
        });

        static::getFixtureInstance('main_visual_image')->unload();
        static::getFixtureInstance('main_visual')->initTable();
        static::getFixtureInstance('main_visual_image')->load();
    }

    /**
     * 新規、更新状態を再現したモデルを作る
     * 更新モデルの場合は画像実体も準備する
     * @param bool $isNew
     * @return MainVisualImageForm
     */
    private function formModel($isNew): MainVisualImageForm
    {
        /** @var MainVisual $mainVisual */
        $mainVisual = MainVisual::find()->joinWith(['images'])->where([
            'not',
            [MainVisualImage::tableName() . '.id' => null],
        ])->one();
        if ($isNew) {
            // 新規の場合は一旦unlinkAllする
            $mainVisual->unlinkAll('images', true);
            $mainVisualImage = new MainVisualImage();
        } else {
            $mainVisualImage = $mainVisual->images[0];
            // それぞれの画像が存在する状態にする
            $uploader = $this->uploader();
            $stream = fopen(Yii::getAlias('@app') . '/tests/codeception/_data/test.png', 'r');
            $uploader->fileSystem->writeStream($uploader->storageDir() . '/' . $mainVisualImage->file_name, $stream);
            $uploader->fileSystem->writeStream($uploader->storageDir() . '/' . $mainVisualImage->file_name_sp, $stream);
            fclose($stream);
        }

        return new MainVisualImageForm($mainVisual, $mainVisualImage);
    }

    /**
     * test用のattributesと、画像postを再現してそれぞれload
     * @param MainVisualImageForm $model
     * @param string $attribute 画像の入るMainVisualImageFormのattribute
     */
    private function load(MainVisualImageForm $model, string $attribute)
    {
        $this->loadFilePost($model->formName(), $attribute);
        $model->$attribute = UploadedFile::getInstance($model, $attribute);
        $model->load([$model->formName() => self::ATTRIBUTES]);
    }

    /**
     * 画像とレコードの登録を確認
     * @param MainVisualImageForm $model
     * @param string $attribute 画像の入るmainVisualImageのattribute
     */
    private function verifySave(MainVisualImageForm $model, string $attribute)
    {
        $uploader = $this->uploader();
        verify($uploader->hasStorage($model->mainVisualImage->$attribute))->true();
        foreach (self::ATTRIBUTES as $name => $value) {
            verify($model->mainVisualImage->$name)->equals($value);
        }
        $this->tester->seeInDatabase(MainVisualImage::tableName(), $model->mainVisualImage->attributes);
    }

    /**
     * @return Uploader
     */
    private function uploader():Uploader
    {
        $uploader = new Uploader();
        $uploader->dirPath = WidgetData::DIR_PATH;
        return $uploader;
    }
}
